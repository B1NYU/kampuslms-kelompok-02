#!/usr/bin/env bash
# Bukti IDOR tertutup di server sungguhan.
# Pakai:  php artisan migrate:fresh --seed && php artisan serve   (terminal 1)
#         bash docs/uji-idor-curl.sh                               (terminal 2)
BASE="${BASE:-http://127.0.0.1:8000}"
JAR=$(mktemp)
trap 'rm -f "$JAR"' EXIT

login() { # $1=identifier $2=password
  : > "$JAR"
  local token
  token=$(curl -s -c "$JAR" "$BASE/" | grep -o 'name="_token" value="[^"]*"' | head -1 | sed 's/.*value="//;s/"//')
  curl -s -o /dev/null -b "$JAR" -c "$JAR" -X POST "$BASE/login" \
       --data-urlencode "_token=$token" --data-urlencode "identifier=$1" --data-urlencode "password=$2"
}
code() { curl -s -o /dev/null -w "%{http_code}" -b "$JAR" "$BASE$1"; }
check() { # $1=label $2=harapan $3=aktual
  [ "$2" = "$3" ] && s="OK  " || s="GAGAL"
  printf "%s %-58s harapan=%s aktual=%s\n" "$s" "$1" "$2" "$3"
}

# ID pengumpulan milik mahasiswa demo (10240000) dan milik orang lain
read OWN OTHER < <(php artisan tinker --execute='
$me = App\Models\User::where("nim_nip","10240000")->first();
echo (App\Models\Submission::where("user_id",$me->id)->value("id") ?? 0) . " " .
     (App\Models\Submission::where("user_id","!=",$me->id)->value("id") ?? 0);' 2>/dev/null | tail -1)
echo "Submission milik sendiri=$OWN, milik orang lain=$OTHER"; echo

echo "== Tamu (belum login) =="
: > "$JAR"
check "GET /submissions/$OTHER (diarahkan ke login)" 302 "$(code /submissions/$OTHER)"

echo "== Mahasiswa demo =="
login 10240000 password
[ "$OWN" != 0 ]   && check "GET /submissions/$OWN (milik sendiri)"       200 "$(code /submissions/$OWN)"
check "GET /submissions/$OTHER (milik orang lain)"                        403 "$(code /submissions/$OTHER)"
check "GET /submissions/999999 (tidak ada)"                               404 "$(code /submissions/999999)"
check "GET /admin/pengguna (area admin)"                                  403 "$(code /admin/pengguna)"
check "GET /dosen/tugas (area dosen)"                                     403 "$(code /dosen/tugas)"

echo "== Dosen demo (pengampu?) =="
login NIP-000 password
check "GET /admin/pengguna (area admin)"                                  403 "$(code /admin/pengguna)"
echo "(status /submissions/$OTHER untuk dosen tergantung apakah ia pengampu: 200 bila ya, 403 bila tidak)"
echo "   aktual: $(code /submissions/$OTHER)"

echo "== Admin =="
login admin@kampuslms.test password
check "GET /submissions/$OTHER"                                           200 "$(code /submissions/$OTHER)"
check "GET /admin/pengguna"                                               200 "$(code /admin/pengguna)"
