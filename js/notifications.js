function formatNotificationTime(dateString) {
    const createdAt = new Date(dateString);
    const now = new Date();
    const diffMs = now - createdAt;
    const diffMinutes = Math.max(0, Math.floor(diffMs / 60000));

    if (diffMinutes < 1) {
        return "À l'instant";
    }
    if (diffMinutes < 60) {
        return `Il y a ${diffMinutes} min`;
    }

    const diffHours = Math.floor(diffMinutes / 60);
    if (diffHours < 24) {
        return `Il y a ${diffHours} h`;
    }

    const diffDays = Math.floor(diffHours / 24);
    if (diffDays < 7) {
        return `Il y a ${diffDays} j`;
    }

    return createdAt.toLocaleDateString('fr-FR');
}

function buildNotificationMessage(notification) {
    const data = notification.data || {};

    if (notification.type === 'commande_vendeur') {
        return {
            title: data.title || 'Nouvelle commande vendeur',
            message: data.message || `Commande ${data.reference || ''} créée par ${data.vendeur_nom || 'un vendeur'}`
        };
    }

    if (notification.type === 'stock_bas') {
        return {
            title: data.title || 'Alerte stock bas',
            message: data.message || 'Un ou plusieurs produits ont atteint le seuil critique.'
        };
    }

    return {
        title: data.title || 'Notification',
        message: data.message || 'Nouvelle activité détectée.'
    };
}

async function deleteAdminNotification(notificationId) {
    const formData = new FormData();
    formData.append('notification_id', notificationId);

    const response = await fetch('../php/post_delete_notification.php', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();

    if (!result.success) {
        throw new Error(result.message || 'Suppression impossible');
    }

    return result;
}

async function loadAdminNotifications() {
    const notifBtn = document.getElementById('notif-btn');
    const notifDropdown = document.getElementById('notif-dropdown');
    const notifList = document.getElementById('notif-list');
    const notifCount = document.getElementById('notif-count');
    const notifDot = document.getElementById('notif-dot');
    const notifEmpty = document.getElementById('notif-empty');

    if (!notifBtn || !notifDropdown || !notifList || !notifCount || !notifDot) {
        return;
    }

    if (notifBtn.dataset.role !== 'admin') {
        notifCount.classList.add('hidden');
        notifDot.classList.add('hidden');
        notifList.innerHTML = '';
        if (notifEmpty) {
            notifEmpty.classList.remove('hidden');
            notifEmpty.textContent = 'Aucune notification disponible pour ce profil.';
        }
        return;
    }

    try {
        const response = await fetch('../php/post_read_notifications.php?limit=12');
        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'Erreur notifications');
        }

        const notifications = result.notifications || [];
        const unreadCount = Number(result.unread_count || 0);

        notifList.innerHTML = '';

        if (!notifications.length) {
            if (notifEmpty) {
                notifEmpty.classList.remove('hidden');
            }
        } else if (notifEmpty) {
            notifEmpty.classList.add('hidden');
        }

        notifications.forEach((notification) => {
            const summary = buildNotificationMessage(notification);
            const item = document.createElement('div');
            item.className = `w-full p-3 transition hover:bg-gray-50 dark:hover:bg-slate-600/50 ${notification.read_status ? '' : 'bg-blue-50/60 dark:bg-slate-600/20'}`;
            item.innerHTML = `
                <div class="flex items-start gap-3">
                    <span class="mt-1 inline-block h-2.5 w-2.5 rounded-full ${notification.read_status ? 'bg-slate-300' : 'bg-blue-500'}"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-700 dark:text-slate-200">${summary.title}</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-slate-300">${summary.message}</p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-slate-500">${formatNotificationTime(notification.created_at)}</p>
                    </div>
                    <button type="button" class="notif-delete-btn flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-950/30" data-id="${notification.id}" title="Supprimer la notification" aria-label="Supprimer la notification">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            `;

            item.addEventListener('click', (event) => {
                if (event.target.closest('.notif-delete-btn')) {
                    return;
                }

                const commandeId = notification.data?.commande_id;
                if (commandeId) {
                    window.location.href = `../view/commande_view.php?commande=${commandeId}`;
                }
            });

            const deleteButton = item.querySelector('.notif-delete-btn');
            if (deleteButton) {
                deleteButton.addEventListener('click', async (event) => {
                    event.preventDefault();
                    event.stopPropagation();

                    try {
                        await deleteAdminNotification(notification.id);
                        await loadAdminNotifications();
                    } catch (error) {
                        console.error('Erreur suppression notification:', error);
                    }
                });
            }

            notifList.appendChild(item);
        });

        notifCount.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
        notifCount.classList.toggle('hidden', unreadCount === 0);
        notifDot.classList.toggle('hidden', unreadCount === 0);
    } catch (error) {
        console.error('Erreur chargement notifications:', error);
        notifList.innerHTML = '';
        if (notifEmpty) {
            notifEmpty.classList.remove('hidden');
            notifEmpty.textContent = 'Impossible de charger les notifications.';
        }
        notifCount.classList.add('hidden');
        notifDot.classList.add('hidden');
    }
}

async function markNotificationsAsRead() {
    const notifBtn = document.getElementById('notif-btn');
    if (!notifBtn || notifBtn.dataset.role !== 'admin') {
        return;
    }

    try {
        await fetch('../php/post_mark_notifications_read.php', {
            method: 'POST'
        });
    } catch (error) {
        console.error('Erreur mise à jour notifications:', error);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const notifBtn = document.getElementById('notif-btn');
    if (!notifBtn) {
        return;
    }

    loadAdminNotifications();

    notifBtn.addEventListener('click', async () => {
        await markNotificationsAsRead();
        setTimeout(() => {
            loadAdminNotifications();
        }, 150);
    });

    if (notifBtn.dataset.role === 'admin') {
        setInterval(loadAdminNotifications, 30000);
    }
});
