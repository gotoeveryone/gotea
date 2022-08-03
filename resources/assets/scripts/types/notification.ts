export interface NotificationCondition {
  limit: number;
  page: number;
}

export interface NotificationListResponse {
  response: NotificationList;
}

export interface NotificationResponse {
  response: Notification;
}

export interface NotificationList {
  total: number;
  items: Notification[];
}

export interface Notification {
  id: number;
  title: string;
  content: string;
  is_draft: boolean;
  published: string;
  is_permanent: boolean;
}
