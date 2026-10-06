#!/bin/bash

# ==========================================
# KONFIGURASI AWAL
# ==========================================
BASE_URL="http://localhost:8000/api/v1"

# Wajib diisi dengan token & ID riil dari database/seeder Anda:
TOKEN_DOSEN_A="3|5hejeqHxbwhXlDBuCykc2NAg4JhYnLLyav6Ndx3n60c5c8a6"
TOKEN_DOSEN_B="1|Y7akhudnNiON3dp7qfN0di7L4L8BwVwBFMcHo3dmb39a4b5d"
TOKEN_MAHASISWA="2|bU2i5ionq96cW7RHbcussGlyIWUCWfo3yj9hlFni3614a034"

COURSE_DOSEN_A_ID=2
COURSE_DOSEN_B_ID=4
ASSIGNMENT_DOSEN_A_ID=5
ASSIGNMENT_DOSEN_B_ID=10

# Color format untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Helper function untuk mengirim request dan mengecek HTTP Status Code
test_endpoint() {
    local label="$1"
    local expected_code="$2"
    local method="$3"
    local url="$4"
    local token="$5"
    local data="$6"

    local auth_header=""
    if [ -n "$token" ]; then
        auth_header="-H \"Authorization: Bearer $token\""
    fi

    local data_header=""
    if [ -n "$data" ]; then
        data_header="-d '$data'"
    fi

    # Jalankan curl
    local http_code
    if [ "$method" == "GET" ] || [ "$method" == "DELETE" ]; then
        http_code=$(curl -s -o /dev/null -w "%{http_code}" -X "$method" "$url" \
            -H "Accept: application/json" \
            ${token:+-H "Authorization: Bearer $token"})
    else
        http_code=$(curl -s -o /dev/null -w "%{http_code}" -X "$method" "$url" \
            -H "Accept: application/json" \
            -H "Content-Type: application/json" \
            ${token:+-H "Authorization: Bearer $token"} \
            ${data:+-d "$data"})
    fi

    # Verifikasi Status Code
    if [ "$http_code" -eq "$expected_code" ]; then
        echo -e "[${GREEN}PASSED${NC}] $label | Expected: $expected_code | Got: $http_code"
    else
        echo -e "[${RED}FAILED${NC}] $label | Expected: $expected_code | Got: $http_code"
    fi
}

echo "=================================================="
echo "          STARTING AUTHORIZATION TESTS            "
echo "=================================================="

# ==================================================
# 1. POST /auth/logout
# ==================================================
echo -e "\n--- Testing: POST /auth/logout ---"
test_endpoint "1. Tanpa token" 401 POST "$BASE_URL/auth/logout"
test_endpoint "2. Token Mahasiswa" 200 POST "$BASE_URL/auth/logout" "$TOKEN_MAHASISWA" # Logout berlaku untuk semua role yang login
test_endpoint "4. Token Dosen A" 200 POST "$BASE_URL/auth/logout" "$TOKEN_DOSEN_A"

# ==================================================
# 2. GET /me
# ==================================================
echo -e "\n--- Testing: GET /me ---"
test_endpoint "1. Tanpa token" 401 GET "$BASE_URL/me"
test_endpoint "4. Token Mahasiswa (Valid)" 200 GET "$BASE_URL/me" "$TOKEN_MAHASISWA"
test_endpoint "4. Token Dosen A (Valid)" 200 GET "$BASE_URL/me" "$TOKEN_DOSEN_A"

# ==================================================
# 3. GET /courses
# ==================================================
echo -e "\n--- Testing: GET /courses ---"
test_endpoint "1. Tanpa token" 401 GET "$BASE_URL/courses"
test_endpoint "4. Token Mahasiswa (Valid - Lihat mata kuliah diikuti)" 200 GET "$BASE_URL/courses" "$TOKEN_MAHASISWA"
test_endpoint "4. Token Dosen A (Valid - Lihat mata kuliah diajar)" 200 GET "$BASE_URL/courses" "$TOKEN_DOSEN_A"

# ==================================================
# 4. GET /courses/{course}
# ==================================================
echo -e "\n--- Testing: GET /courses/{course} ---"
test_endpoint "1. Tanpa token" 401 GET "$BASE_URL/courses/$COURSE_DOSEN_A_ID"
test_endpoint "3. Dosen B akses Course milik Dosen A" 403 GET "$BASE_URL/courses/$COURSE_DOSEN_A_ID" "$TOKEN_DOSEN_B"
test_endpoint "4. Dosen A akses Course milik sendiri" 200 GET "$BASE_URL/courses/$COURSE_DOSEN_A_ID" "$TOKEN_DOSEN_A"

# ==================================================
# 5. GET /courses/{course}/assignments
# ==================================================
echo -e "\n--- Testing: GET /courses/{course}/assignments ---"
test_endpoint "1. Tanpa token" 401 GET "$BASE_URL/courses/$COURSE_DOSEN_A_ID/assignments"
test_endpoint "3. Dosen B akses Assignment Course Dosen A" 403 GET "$BASE_URL/courses/$COURSE_DOSEN_A_ID/assignments" "$TOKEN_DOSEN_B"
test_endpoint "4. Mahasiswa terdaftar/Dosen A pemilik Course" 200 GET "$BASE_URL/courses/$COURSE_DOSEN_A_ID/assignments" "$TOKEN_DOSEN_A"

# ==================================================
# 6. POST /assignments
# ==================================================
echo -e "\n--- Testing: POST /assignments ---"
PAYLOAD_ASSIGNMENT='{"course_id":'$COURSE_DOSEN_A_ID',"title":"Tugas Baru","description":"Deskripsi"}'

test_endpoint "1. Tanpa token" 401 POST "$BASE_URL/assignments" "" "$PAYLOAD_ASSIGNMENT"
test_endpoint "2. Mahasiswa membuat assignment" 403 POST "$BASE_URL/assignments" "$TOKEN_MAHASISWA" "$PAYLOAD_ASSIGNMENT"
test_endpoint "3. Dosen B membuat assignment di Course Dosen A" 403 POST "$BASE_URL/assignments" "$TOKEN_DOSEN_B" "$PAYLOAD_ASSIGNMENT"
test_endpoint "4. Dosen A membuat assignment di Course sendiri" 201 POST "$BASE_URL/assignments" "$TOKEN_DOSEN_A" "$PAYLOAD_ASSIGNMENT"

# ==================================================
# 7. PUT/PATCH /assignments/{assignment}
# ==================================================
echo -e "\n--- Testing: PUT /assignments/{assignment} ---"
PAYLOAD_UPDATE='{"title":"Tugas Diperbarui"}'

test_endpoint "1. Tanpa token" 401 PUT "$BASE_URL/assignments/$ASSIGNMENT_DOSEN_A_ID" "" "$PAYLOAD_UPDATE"
test_endpoint "2. Mahasiswa mengedit assignment" 403 PUT "$BASE_URL/assignments/$ASSIGNMENT_DOSEN_A_ID" "$TOKEN_MAHASISWA" "$PAYLOAD_UPDATE"
test_endpoint "3. Dosen B mengedit assignment milik Dosen A" 403 PUT "$BASE_URL/assignments/$ASSIGNMENT_DOSEN_A_ID" "$TOKEN_DOSEN_B" "$PAYLOAD_UPDATE"
test_endpoint "4. Dosen A mengedit assignment milik sendiri" 200 PUT "$BASE_URL/assignments/$ASSIGNMENT_DOSEN_A_ID" "$TOKEN_DOSEN_A" "$PAYLOAD_UPDATE"

# ==================================================
# 8. DELETE /assignments/{assignment}
# ==================================================
echo -e "\n--- Testing: DELETE /assignments/{assignment} ---"
test_endpoint "1. Tanpa token" 401 DELETE "$BASE_URL/assignments/$ASSIGNMENT_DOSEN_A_ID"
test_endpoint "2. Mahasiswa menghapus assignment" 403 DELETE "$BASE_URL/assignments/$ASSIGNMENT_DOSEN_A_ID" "$TOKEN_MAHASISWA"
test_endpoint "3. Dosen B menghapus assignment milik Dosen A" 403 DELETE "$BASE_URL/assignments/$ASSIGNMENT_DOSEN_A_ID" "$TOKEN_DOSEN_B"
test_endpoint "4. Dosen A menghapus assignment milik sendiri" 200 DELETE "$BASE_URL/assignments/$ASSIGNMENT_DOSEN_A_ID" "$TOKEN_DOSEN_A"

echo "=================================================="
echo "                 TESTS COMPLETED                  "
echo "=================================================="