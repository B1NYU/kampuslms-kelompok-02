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
        const initials = initialsOf(u.name);
        const isNonAktif = u.status === 'nonaktif';
        
        let roleBadge = '';
        if (u.role === 'admin') {
            roleBadge = `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-orange-50 text-orange-600 border border-orange-200/70">👑 Admin</span>`;
        } else if (u.role === 'dosen') {
            roleBadge = `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200/70">🎓 Dosen</span>`;
        } else {
            roleBadge = `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-sky-50 text-[#039FFA] border border-sky-200/70">👥 Mahasiswa</span>`;
        }

        const statusBadge = isNonAktif
            ? `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-500 border border-slate-200/70"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Non-aktif</span>`
            : `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-200/70"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Aktif</span>`;

        let avatarBg = 'bg-sky-50 text-[#039FFA] border-sky-100';
        if (u.role === 'admin') avatarBg = 'bg-orange-50 text-orange-600 border-orange-100';
        else if (u.role === 'dosen') avatarBg = 'bg-amber-50 text-amber-600 border-amber-100';

        return `
            <td class="py-3.5 px-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs shrink-0 border ${avatarBg}">
                        ${initials}
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="font-bold text-slate-900 text-sm leading-snug">${u.name}</span>
                        <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-500">
                            <span>${u.nim_nip || '-'}</span>
                            ${u.email ? `<span class="text-slate-300">•</span><span class="truncate max-w-[200px] text-slate-400">${u.email}</span>` : ''}
                        </div>
                    </div>
                </div>
            </td>
            <td class="py-3.5 px-5">${roleBadge}</td>
            <td class="py-3.5 px-5">${statusBadge}</td>
            <td class="py-3.5 px-5 text-right">
                <div class="inline-flex items-center justify-end gap-1.5">
                    <button type="button" class="btn-edit-user p-2 rounded-xl border border-slate-200/80 bg-white text-slate-600 hover:text-[#039FFA] hover:border-sky-300 hover:bg-sky-50/60 shadow-xs transition-all cursor-pointer" title="Edit pengguna">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button type="button" class="btn-delete-user p-2 rounded-xl border border-slate-200/80 bg-white text-slate-600 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50/60 shadow-xs transition-all cursor-pointer" title="Hapus pengguna">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                        </svg>
                    </button>
                </div>
            </td>`;
    }

    function upsertRow(u) {
        if (!tbody) return;
        let tr = tbody.querySelector(`tr[data-id="${u.id}"]`);
        const emptyRow = document.getElementById('userEmptyRow');
        if (emptyRow) emptyRow.remove();

        if (!tr) {
            tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/80 transition-colors';
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
                b.classList.remove('active', 'bg-[#039FFA]', 'text-white', 'shadow-sm', 'border-transparent');
                b.classList.add('bg-white', 'text-slate-600', 'border-slate-200/80', 'hover:bg-slate-50', 'hover:text-slate-900');
                const badge = b.querySelector('span[id^="filterCount"]');
                if (badge) {
                    badge.classList.remove('bg-white/20');
                    badge.classList.add('bg-slate-100', 'text-slate-600');
                }
            });
            this.classList.remove('bg-white', 'text-slate-600', 'border-slate-200/80', 'hover:bg-slate-50', 'hover:text-slate-900');
            this.classList.add('active', 'bg-[#039FFA]', 'text-white', 'shadow-sm', 'border-transparent');
            const activeBadge = this.querySelector('span[id^="filterCount"]');
            if (activeBadge) {
                activeBadge.classList.remove('bg-slate-100', 'text-slate-600');
                activeBadge.classList.add('bg-white/20');
            }

            currentRoleFilter = this.dataset.filter;
            applyFilters();
        });
    });

    document.getElementById('searchUserInput')?.addEventListener('input', applyFilters);
});
