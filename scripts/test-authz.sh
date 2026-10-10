#!/usr/bin/env bash

BASE_URL="http://localhost:8000"
COOKIE_JAR="/tmp/auth_cookies.txt"

# Kredensial Pengujian (Sesuai DatabaseSeeder)
ADMIN_IDENTIFIER="admin@kampuslms.test"
DOSEN_IDENTIFIER="dosen@kampuslms.test"
MAHASISWA_IDENTIFIER="mahasiswa@kampuslms.test"
PASSWORD="password"

# Resource ID Deterministik dari DatabaseSeeder
COURSE_ID_UNENROLLED="2"   # MK 2 diampu Dosen B, Mahasiswa Demo TIDAK terdaftar
ASSIGNMENT_ID_OTHER="3"    # Assignment di MK 2
SUBMISSION_ID_OTHER="2"    # Submission dummy

# Helper Ambil CSRF Token dari Form Login
get_csrf_token() {
    curl -s -c "$COOKIE_JAR" -b "$COOKIE_JAR" "$BASE_URL/" \
        | grep -o 'name="_token" value="[^"]*"' \
        | head -n 1 \
        | cut -d'"' -f4
}

# Helper Login Web
login_web() {
    local identifier="$1"
    rm -f "$COOKIE_JAR"
    
    local csrf_token
    csrf_token=$(get_csrf_token)

    # Menggunakan nama input 'identifier' (bukan 'email') sesuai AuthController
    curl -s -o /dev/null -X POST \
        -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
        -H "Content-Type: application/x-www-form-urlencoded" \
        -d "_token=$csrf_token" \
        -d "identifier=$identifier" \
        -d "password=$PASSWORD" \
        "$BASE_URL/login"
}

run_test() {
    local title="$1"
    local method="$2"
    local endpoint="$3"
    local expected="$4"
    local data="$5"

    echo -n "Testing: $title ... "

    local csrf_token=""
    if [ "$method" != "GET" ]; then
        csrf_token=$(get_csrf_token)
    fi

    local status_code
    status_code=$(curl -s -o /dev/null -w "%{http_code}" -X "$method" \
        -b "$COOKIE_JAR" -c "$COOKIE_JAR" \
        -H "Accept: application/json" \
        -H "X-CSRF-TOKEN: $csrf_token" \
        ${data:+-H "Content-Type: application/json" -d "$data"} \
        "$BASE_URL$endpoint")

    if [ "$status_code" -eq "$expected" ]; then
        echo -e "\e[32mPASSED (HTTP $status_code)\e[0m"
    else
        echo -e "\e[31mFAILED (Expected $expected, got $status_code)\e[0m"
    fi
}

echo "=================================================="
echo "      SKRIP PENGUJIAN OTORISASI (AUTHZ TEST)      "
echo "=================================================="

# 1. MAHASISWA
echo -e "\n[1] MENGUJI AKSES ROLE: MAHASISWA"
login_web "$MAHASISWA_IDENTIFIER"

run_test "Mahasiswa mendaftar submission mahasiswa lain" "GET" "/submissions/$SUBMISSION_ID_OTHER" 403
run_test "Mahasiswa mengunduh submission mahasiswa lain" "GET" "/submissions/$SUBMISSION_ID_OTHER/unduh" 403
run_test "Mahasiswa membuka detail MK tidak terdaftar (Web)" "GET" "/mahasiswa/mata-kuliah/$COURSE_ID_UNENROLLED" 403
run_test "Mahasiswa membuka tugas MK tidak terdaftar" "GET" "/assignments/$ASSIGNMENT_ID_OTHER" 403
run_test "Mahasiswa mengubah role via endpoint Admin User" "PUT" "/admin/pengguna/1" 403 '{"role":"admin"}'

# 2. DOSEN
echo -e "\n[2] MENGUJI AKSES ROLE: DOSEN"
login_web "$DOSEN_IDENTIFIER"

run_test "Dosen menghapus mahasiswa dari MK lain" "DELETE" "/dosen/mahasiswa/$COURSE_ID_UNENROLLED/1" 403
run_test "Dosen mengakses dashboard Admin" "GET" "/admin/dashboard" 403
run_test "Dosen mengubah role via Admin User Endpoint" "PUT" "/admin/pengguna/1" 403 '{"role":"admin"}'

# 3. ADMIN
echo -e "\n[3] MENGUJI AKSES ROLE: ADMIN"
login_web "$ADMIN_IDENTIFIER"

run_test "Admin mengakses dashboard Admin" "GET" "/admin/dashboard" 200

echo -e "\n=================================================="
echo "            PENGUJIAN SELESAI                     "
echo "=================================================="