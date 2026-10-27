/**
 * Script untuk Manajemen Akun Pengguna oleh Admin
 */
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const routesEl = document.getElementById('penggunaRoutes');
    if (!routesEl) return;

    const storeUrl          = routesEl.dataset.storeUrl || '';
    const updateUrlTemplate = routesEl.dataset.updateUrlTemplate || '';
    const deleteUrlTemplate = routesEl.dataset.deleteUrlTemplate || '';

    const form             = document.getElementById('formAddUser');
    const tbody            = document.getElementById('userTableBody');
    const alertBox         = document.getElementById('userFormAlert');
    const pageAlertBox     = document.getElementById('userPageAlert');
    const submitBtn        = document.getElementById('btnSubmitUser');
    const cancelBtn        = document.getElementById('userCancelEditBtn');
    const formTitle        = document.getElementById('formUserTitle')?.querySelector('span');
    const modalSubtitle    = document.getElementById('formUserSubtitle');
    const submitLabel      = submitBtn?.querySelector('span');
    const passwordMark     = document.getElementById('passwordRequiredMark');
    const passwordHint     = document.getElementById('passwordHint');

    const userModal        = document.getElementById('userModalOverlay');
    const btnOpenAddUser   = document.getElementById('btnOpenAddUserModal');
    const btnCloseUserModal= document.getElementById('btnCloseUserModal');

    const roleColors = {
        admin: ['rgba(249, 99, 5, 0.12)', '#F96305'],
        dosen: ['rgba(249, 184, 4, 0.14)', '#D97706'],
        mahasiswa: ['rgba(3, 159, 250, 0.12)', '#039FFA'],
    };

    function openUserModal() {
        if (userModal) userModal.classList.add('active');
    }

    function closeUserModal() {
        if (userModal) userModal.classList.remove('active');
    }

    if (btnOpenAddUser) {
        btnOpenAddUser.addEventListener('click', () => {
            resetForm();
            openUserModal();
        });
    }

    if (btnCloseUserModal) btnCloseUserModal.addEventListener('click', closeUserModal);

    if (userModal) {
        userModal.addEventListener('click', (e) => {
            if (e.target === userModal) closeUserModal();
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && userModal && userModal.classList.contains('active')) {
            closeUserModal();
        }
    });

    function showAlert(message, type = 'error') {
        if (!alertBox) return;
        alertBox.style.display = 'block';
        alertBox.style.background = type === 'error' ? '#FEE2E2' : '#DCFCE7';
        alertBox.style.color = type === 'error' ? '#B91C1C' : '#166534';
        alertBox.textContent = message;
        setTimeout(() => { alertBox.style.display = 'none'; }, 4000);
    }

    function showPageAlert(message, type = 'success') {
        if (!pageAlertBox) return;
        pageAlertBox.style.display = 'block';
        pageAlertBox.style.background = type === 'error' ? '#FEE2E2' : '#DCFCE7';
        pageAlertBox.style.color = type === 'error' ? '#B91C1C' : '#166534';
        pageAlertBox.style.border = type === 'error' ? '1px solid #FECACA' : '1px solid #A7F3D0';
        pageAlertBox.textContent = (type === 'error' ? '⚠️ ' : '✅ ') + message;
        setTimeout(() => { pageAlertBox.style.display = 'none'; }, 4000);
    }

    function initialsOf(name) {
        return name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
    }

    function rowHtml(u) {
        const [bg, c] = roleColors[u.role] || ['#F1F5F9', '#64748B'];
        const isNonAktif = u.status === 'nonaktif';
        return `
            <td><div class="user-cell"><div class="user-avatar" style="background:${bg};color:${c};">${initialsOf(u.name)}</div><div class="user-meta"><span class="user-name">${u.name}</span><span class="user-id">${u.nim_nip}</span></div></div></td>
            <td><span class="badge-role badge-role-${u.role}">${u.role.charAt(0).toUpperCase()+u.role.slice(1)}</span></td>
            <td><span class="badge-status badge-status-${isNonAktif ? 'inactive' : 'active'}">${isNonAktif ? 'Non-aktif' : 'Aktif'}</span></td>
            <td><div class="btn-actions">
                <button type="button" class="btn-icon btn-icon-edit btn-edit-user"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>
                <button type="button" class="btn-icon btn-icon-danger btn-delete-user"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg></button>
            </div></td>`;
    }

    function upsertRow(u) {
        if (!tbody) return;
        let tr = tbody.querySelector(`tr[data-id="${u.id}"]`);
        const emptyRow = document.getElementById('userEmptyRow');
        if (emptyRow) emptyRow.remove();

        if (!tr) {
            tr = document.createElement('tr');
            tbody.prepend(tr);
        }
        tr.dataset.id = u.id;
        tr.dataset.name = u.name;
        tr.dataset.nimNip = u.nim_nip;
        tr.dataset.email = u.email ?? '';
        tr.dataset.role = u.role;
        tr.dataset.status = u.status;
        tr.innerHTML = rowHtml(u);

        bindRowButtons(tr);
        updateCount();
    }

    function setEditMode(isEdit) {
        if (!submitLabel || !cancelBtn || !formTitle || !passwordMark || !passwordHint) return;
        if (isEdit) {
            submitLabel.textContent = 'Update Pengguna';
            cancelBtn.style.display = 'inline-block';
            formTitle.textContent = 'Edit Pengguna';
            if (modalSubtitle) modalSubtitle.textContent = 'Perbarui data akun pengguna yang dipilih';
            const userPassword = document.getElementById('userPassword');
            if (userPassword) userPassword.required = false;
            passwordMark.style.display = 'none';
            passwordHint.style.display = 'block';
        } else {
            submitLabel.textContent = 'Simpan Pengguna';
            cancelBtn.style.display = 'none';
            formTitle.textContent = 'Tambah Pengguna Baru';
            if (modalSubtitle) modalSubtitle.textContent = 'Kelola akun mahasiswa, dosen, atau admin';
            const userPassword = document.getElementById('userPassword');
            if (userPassword) userPassword.required = true;
            passwordMark.style.display = 'inline';
            passwordHint.style.display = 'none';
        }
    }

    function resetForm() {
        if (!form) return;
        form.reset();
        const userDbId = document.getElementById('userDbId');
        if (userDbId) userDbId.value = '';
        setEditMode(false);
    }

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const id = document.getElementById('userDbId')?.value;
            const payload = {
                name: document.getElementById('userNama')?.value.trim(),
                nim_nip: document.getElementById('userId')?.value.trim(),
                email: document.getElementById('userEmail')?.value.trim(),
                role: document.getElementById('userRole')?.value,
                password: document.getElementById('userPassword')?.value,
                status: document.getElementById('userStatus')?.value,
            };

            const url = id ? updateUrlTemplate.replace('__ID__', id) : storeUrl;
            const method = id ? 'PUT' : 'POST';

            if (submitBtn) submitBtn.disabled = true;

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                const result = await res.json();

                if (!res.ok) {
                    if (res.status === 422) {
                        const firstError = Object.values(result.errors)[0][0];
                        showAlert(firstError, 'error');
                    } else {
                        showAlert(result.message || 'Terjadi kesalahan.', 'error');
                    }
                    return;
                }

                upsertRow(result.data);
                showPageAlert(result.message, 'success');
                resetForm();
                closeUserModal();
            } catch (err) {
                showAlert('Gagal terhubung ke server.', 'error');
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            resetForm();
            closeUserModal();
        });
    }

    function bindRowButtons(tr) {
        const editBtn = tr.querySelector('.btn-edit-user');
        if (editBtn) {
            editBtn.addEventListener('click', function () {
                const dbId = document.getElementById('userDbId');
                const uNama = document.getElementById('userNama');
                const uId = document.getElementById('userId');
                const uEmail = document.getElementById('userEmail');
                const uRole = document.getElementById('userRole');
                const uStatus = document.getElementById('userStatus');
                const uPw = document.getElementById('userPassword');

                if (dbId) dbId.value = tr.dataset.id;
                if (uNama) uNama.value = tr.dataset.name;
                if (uId) uId.value = tr.dataset.nimNip;
                if (uEmail) uEmail.value = tr.dataset.email;
                if (uRole) uRole.value = tr.dataset.role;
                if (uStatus) uStatus.value = tr.dataset.status;
                if (uPw) uPw.value = '';

                setEditMode(true);
                openUserModal();
            });
        }

        const deleteBtn = tr.querySelector('.btn-delete-user');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', async function () {
                if (!confirm('Hapus pengguna ini?')) return;

                try {
                    const res = await fetch(deleteUrlTemplate.replace('__ID__', tr.dataset.id), {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    });
                    const result = await res.json();

                    if (!res.ok) {
                        showPageAlert(result.message || 'Gagal menghapus data.', 'error');
                        return;
                    }

                    tr.remove();
                    updateCount();
                    showPageAlert(result.message, 'success');

                    if (tbody && !tbody.querySelector('tr[data-id]')) {
                        tbody.innerHTML = `<tr id="userEmptyRow"><td colspan="4" style="text-align:center;padding:20px;color:#94A3B8;">Belum ada pengguna terdaftar.</td></tr>`;
                    }
                } catch (err) {
                    showPageAlert('Gagal terhubung ke server.', 'error');
                }
            });
        }
    }

    document.querySelectorAll('#userTableBody tr[data-id]').forEach(bindRowButtons);

    let currentRoleFilter = 'all';

    function updateCount() {
        if (!tbody) return;
        const allRows = tbody.querySelectorAll('tr[data-id]');
        const total = allRows.length;
        let adminCount = 0;
        let dosenCount = 0;
        let mhsCount = 0;

        allRows.forEach(r => {
            const role = r.dataset.role;
            if (role === 'admin') adminCount++;
            else if (role === 'dosen') dosenCount++;
            else if (role === 'mahasiswa') mhsCount++;
        });

        const countElem = document.getElementById('userTableCount');
        if (countElem) countElem.textContent = total;

        const bTotal = document.getElementById('badgeCountTotal');
        if (bTotal) bTotal.textContent = total;
        const bAdmin = document.getElementById('badgeCountAdmin');
        if (bAdmin) bAdmin.textContent = adminCount;
        const bDosen = document.getElementById('badgeCountDosen');
        if (bDosen) bDosen.textContent = dosenCount;
        const bMhs = document.getElementById('badgeCountMhs');
        if (bMhs) bMhs.textContent = mhsCount;

        const fAll = document.getElementById('filterCountAll');
        if (fAll) fAll.textContent = total;
        const fAdmin = document.getElementById('filterCountAdmin');
        if (fAdmin) fAdmin.textContent = adminCount;
        const fDosen = document.getElementById('filterCountDosen');
        if (fDosen) fDosen.textContent = dosenCount;
        const fMhs = document.getElementById('filterCountMhs');
        if (fMhs) fMhs.textContent = mhsCount;

        applyFilters();
    }

    function applyFilters() {
        if (!tbody) return;
        const q = (document.getElementById('searchUserInput')?.value || '').toLowerCase().trim();
        const allRows = tbody.querySelectorAll('tr[data-id]');

        allRows.forEach(row => {
            const role = row.dataset.role;
            const matchRole = (currentRoleFilter === 'all' || role === currentRoleFilter);
            const text = row.textContent.toLowerCase();
            const matchSearch = !q || text.includes(q);

            row.style.display = (matchRole && matchSearch) ? '' : 'none';
        });
    }

    document.querySelectorAll('.role-filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.role-filter-btn').forEach(b => {
                b.classList.remove('active');
                b.style.background = 'var(--admin-white)';
                b.style.color = 'var(--admin-text)';
                b.style.borderColor = 'var(--admin-border)';
            });
            this.classList.add('active');
            this.style.background = 'var(--admin-primary)';
            this.style.color = '#fff';
            this.style.borderColor = 'var(--admin-primary)';

            currentRoleFilter = this.dataset.filter;
            applyFilters();
        });
    });

    document.getElementById('searchUserInput')?.addEventListener('input', applyFilters);
});
