const notificationHeaders = {
    headers: {
        Accept: 'application/json',
    },
};

const notificationUrl = (suffix = '') => `/notifications${suffix}`;

export const fetchNotifications = async (limit = 10) => {
    const response = await window.axios.get(
        `${notificationUrl()}?limit=${limit}`,
        notificationHeaders,
    );

    return response.data;
};

export const markNotificationAsRead = async (id) => {
    const response = await window.axios.post(
        notificationUrl(`/${id}/read`),
        {},
        notificationHeaders,
    );

    return response.data;
};

export const markAllNotificationsAsRead = async () => {
    const response = await window.axios.post(
        notificationUrl('/read-all'),
        {},
        notificationHeaders,
    );

    return response.data;
};
