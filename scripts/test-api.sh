#!/usr/bin/env bash
# Uji otorisasi REST API KampusLMS (Minggu 6).
#
# Cara pakai:
#   php artisan migrate:fresh --seed        # data demo deterministik
#   php artisan serve                       # terminal 1
#   bash scripts/test-api.sh                # terminal 2 (Git Bash / WSL / Linux / macOS)
#
# Variabel opsional: BASE=http://127.0.0.1:8000/api/v1  PASSWORD=password
#
# Skrip login sendiri dan mencari ID yang dibutuhkan, tanpa token/ID yang ditulis
# tangan. Login dibatasi 5/menit per IP, jadi skrip memakai tepat 5 login; kalau diulang
# dalam waktu kurang dari semenit, tunggu 60 detik dulu.
#
# Empat kondisi per endpoint:
#   1. tanpa token                              -> 401
#   2. token mahasiswa ke endpoint dosen        -> 403
#   3. token dosen A ke data milik dosen B      -> 403
#   4. token dan hak yang benar                 -> 200 / 201 / 204
# Endpoint baca (GET) tidak punya "endpoint dosen", jadi kondisi 2 diganti
# mahasiswa yang tidak terdaftar di mata kuliah tersebut -> 403.

BASE="${BASE:-http://127.0.0.1:8000/api/v1}"
PASSWORD="${PASSWORD:-password}"

PASS=0; FAIL=0
GREEN='\033[0;32m'; RED='\033[0;31m'; NC='\033[0m'

# req METHOD PATH [TOKEN] [JSON]  -> menyetel $CODE dan $BODY
req() {
  local method="$1" path="$2" token="$3" data="$4" out
  local args=(-s -w $'\n%{http_code}' -X "$method" "$BASE$path" -H "Accept: application/json")
  [ -n "$token" ] && args+=(-H "Authorization: Bearer $token")
  [ -n "$data" ]  && args+=(-H "Content-Type: application/json" -d "$data")
  out=$(curl "${args[@]}")
  CODE="${out##*$'\n'}"
  BODY="${out%$'\n'*}"
}

# check LABEL EXPECTED METHOD PATH [TOKEN] [JSON]
check() {
  local label="$1" expected="$2"; shift 2
  req "$@"
  if [ "$CODE" = "$expected" ]; then
    printf "[${GREEN}OK${NC}]    %-62s harapan=%s aktual=%s\n" "$label" "$expected" "$CODE"; PASS=$((PASS+1))
  else
    printf "[${RED}GAGAL${NC}] %-62s harapan=%s aktual=%s\n" "$label" "$expected" "$CODE"; FAIL=$((FAIL+1))
  fi
}

# Ambil nilai string pertama untuk sebuah kunci JSON (tanpa jq).
json_str() { echo "$BODY" | grep -o "\"$1\":\"[^\"]*\"" | head -1 | sed "s/\"$1\":\"//;s/\"\$//"; }
# Ambil angka pertama untuk sebuah kunci JSON.
json_num() { echo "$BODY" | grep -o "\"$1\":[0-9]*" | head -1 | sed "s/\"$1\"://"; }

login() { # $1 = identifier -> token
  req POST /auth/login "" "{\"identifier\":\"$1\",\"password\":\"$PASSWORD\"}"
  if [ "$CODE" != "200" ]; then
    echo "Login $1 gagal (HTTP $CODE). Sudah 'migrate:fresh --seed'? Server jalan? Bila 429, tunggu 60 detik." >&2
    exit 1
  fi
  json_str token
}

echo "== Persiapan: login akun demo =="
TOKEN_A=$(login "NIP-000")        || exit 1   # Dosen A (pengampu MK 1)
TOKEN_B=$(login "NIP-999")        || exit 1   # Dosen B (pengampu MK 2)
TOKEN_M=$(login "10240000")       || exit 1   # Mahasiswa demo (terdaftar di MK 1, TIDAK di MK 2)

req GET /courses "$TOKEN_A"; COURSE_A=$(json_num id)
req GET /courses "$TOKEN_B"; COURSE_B=$(json_num id)
if [ -z "$COURSE_A" ] || [ -z "$COURSE_B" ]; then
  echo "Mata kuliah demo tidak ditemukan. Jalankan: php artisan migrate:fresh --seed" >&2; exit 1
fi
echo "MK Dosen A = $COURSE_A, MK Dosen B = $COURSE_B"

# ---------------------------------------------------------------------------
echo; echo "== POST /auth/login (publik) =="
# (login yang benar sudah dibuktikan di bagian Persiapan; batas 5/menit membuat login ekstra dihindari)
check "password salah -> 422"                         422 POST /auth/login "" '{"identifier":"10240000","password":"salah"}'
MSG1="$BODY"
check "identitas tidak terdaftar -> 422"              422 POST /auth/login "" '{"identifier":"tidak-ada","password":"salah"}'
MSG2="$BODY"
if [ "$MSG1" = "$MSG2" ]; then
  printf "[${GREEN}OK${NC}]    %-62s (tidak ada user enumeration)\n" "pesan error sama untuk keduanya"; PASS=$((PASS+1))
else
  printf "[${RED}GAGAL${NC}] %-62s\n" "pesan error BERBEDA -> membocorkan email/NIM terdaftar"; FAIL=$((FAIL+1))
fi

# ---------------------------------------------------------------------------
echo; echo "== GET /me =="
check "1. tanpa token -> 401"                         401 GET /me
check "4. mahasiswa -> 200"                           200 GET /me "$TOKEN_M"
case "$BODY" in
  *password*|*remember_token*) printf "[${RED}GAGAL${NC}] %-62s\n" "response /me membocorkan password/remember_token"; FAIL=$((FAIL+1));;
  *) printf "[${GREEN}OK${NC}]    %-62s\n" "response /me tidak memuat password/remember_token"; PASS=$((PASS+1));;
esac

# ---------------------------------------------------------------------------
echo; echo "== GET /courses =="
check "1. tanpa token -> 401"                         401 GET /courses
check "4. mahasiswa -> 200"                           200 GET /courses "$TOKEN_M"
check "4. dosen A -> 200"                             200 GET /courses "$TOKEN_A"
case "$BODY" in *'"meta"'*) printf "[${GREEN}OK${NC}]    %-62s\n" "ada struktur meta (pagination)"; PASS=$((PASS+1));;
  *) printf "[${RED}GAGAL${NC}] %-62s\n" "struktur meta tidak ada"; FAIL=$((FAIL+1));; esac

echo; echo "== GET /courses/{course} =="
check "1. tanpa token -> 401"                         401 GET "/courses/$COURSE_A"
check "2. mahasiswa tidak terdaftar -> 403"           403 GET "/courses/$COURSE_B" "$TOKEN_M"
check "3. dosen A ke MK dosen B -> 403"               403 GET "/courses/$COURSE_B" "$TOKEN_A"
check "4. dosen A ke MK sendiri -> 200"               200 GET "/courses/$COURSE_A" "$TOKEN_A"
check "4. mahasiswa terdaftar -> 200"                 200 GET "/courses/$COURSE_A" "$TOKEN_M"
check "id tidak ada -> 404"                           404 GET "/courses/999999" "$TOKEN_A"

echo; echo "== GET /courses/{course}/assignments =="
check "1. tanpa token -> 401"                         401 GET "/courses/$COURSE_A/assignments"
check "2. mahasiswa tidak terdaftar -> 403"           403 GET "/courses/$COURSE_B/assignments" "$TOKEN_M"
check "3. dosen A ke MK dosen B -> 403"               403 GET "/courses/$COURSE_B/assignments" "$TOKEN_A"
check "4. dosen A -> 200"                             200 GET "/courses/$COURSE_A/assignments" "$TOKEN_A"
check "4. mahasiswa terdaftar -> 200"                 200 GET "/courses/$COURSE_A/assignments" "$TOKEN_M"
case "$BODY" in *'"status":"draft"'*) printf "[${RED}GAGAL${NC}] %-62s\n" "mahasiswa melihat tugas DRAFT"; FAIL=$((FAIL+1));;
  *) printf "[${GREEN}OK${NC}]    %-62s\n" "mahasiswa tidak melihat tugas draft"; PASS=$((PASS+1));; esac

# ---------------------------------------------------------------------------
PAYLOAD="{\"course_id\":$COURSE_A,\"title\":\"Tugas Uji Otorisasi\",\"instructions\":\"Dibuat oleh test-api.sh\",\"due_at\":\"2030-01-01 23:59:00\"}"

echo; echo "== POST /assignments =="
check "1. tanpa token -> 401"                         401 POST /assignments "" "$PAYLOAD"
check "2. mahasiswa -> 403"                           403 POST /assignments "$TOKEN_M" "$PAYLOAD"
check "3. dosen B di MK dosen A -> 403"               403 POST /assignments "$TOKEN_B" "$PAYLOAD"
check "otorisasi sebelum validasi (B + data kosong) -> 403" 403 POST /assignments "$TOKEN_B" "{\"course_id\":$COURSE_A}"
check "validasi gagal (A + data kosong) -> 422"       422 POST /assignments "$TOKEN_A" "{\"course_id\":$COURSE_A}"
check "4. dosen A di MK sendiri -> 201"               201 POST /assignments "$TOKEN_A" "$PAYLOAD"
NEW_ID=$(json_num id)
if [ -z "$NEW_ID" ]; then echo "Tidak bisa membaca ID tugas baru; sisa tes dilewati." >&2; exit 1; fi

echo; echo "== PUT/PATCH /assignments/$NEW_ID =="
FULL="{\"title\":\"Revisi\",\"instructions\":\"Revisi\",\"due_at\":\"2030-02-01 23:59:00\",\"max_score\":100,\"allow_late\":true,\"status\":\"draft\"}"
check "1. tanpa token -> 401"                         401 PUT "/assignments/$NEW_ID" "" "$FULL"
check "2. mahasiswa -> 403"                           403 PUT "/assignments/$NEW_ID" "$TOKEN_M" "$FULL"
check "3. dosen B mengubah tugas dosen A -> 403"      403 PATCH "/assignments/$NEW_ID" "$TOKEN_B" '{"title":"Dibajak"}'
check "PUT tidak lengkap -> 422"                      422 PUT "/assignments/$NEW_ID" "$TOKEN_A" '{"title":"Hanya judul"}'
check "4. PUT lengkap oleh dosen A -> 200"            200 PUT "/assignments/$NEW_ID" "$TOKEN_A" "$FULL"
check "4. PATCH sebagian oleh dosen A -> 200"         200 PATCH "/assignments/$NEW_ID" "$TOKEN_A" '{"title":"Revisi 2"}'

echo; echo "== DELETE /assignments/$NEW_ID =="
check "1. tanpa token -> 401"                         401 DELETE "/assignments/$NEW_ID"
check "2. mahasiswa -> 403"                           403 DELETE "/assignments/$NEW_ID" "$TOKEN_M"
check "3. dosen B menghapus tugas dosen A -> 403"     403 DELETE "/assignments/$NEW_ID" "$TOKEN_B"
check "4. dosen A menghapus tugas sendiri -> 204"     204 DELETE "/assignments/$NEW_ID" "$TOKEN_A"
check "setelah dihapus -> 404"                        404 PUT "/assignments/$NEW_ID" "$TOKEN_A" "$FULL"

# ---------------------------------------------------------------------------
echo; echo "== POST /auth/logout (dijalankan terakhir; mencabut token) =="
check "1. tanpa token -> 401"                         401 POST /auth/logout
check "4. mahasiswa logout -> 200"                    200 POST /auth/logout "$TOKEN_M"
check "token yang sudah dicabut -> 401"               401 GET /me "$TOKEN_M"

# ---------------------------------------------------------------------------
echo
echo "=============================================="
printf " Lulus: %d   Gagal: %d\n" "$PASS" "$FAIL"
echo "=============================================="
[ "$FAIL" -eq 0 ]
