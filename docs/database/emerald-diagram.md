# Emerald Database Schema

```mermaid
erDiagram
    e_assignees {
        int contact_id
        int task_id
    }
    e_attachment_versions {
        int id
        int attachment_id
        datetime created_at
    }
    e_attachments {
        int id
        int user_id
        int task_id
        string filename
    }
    e_comments {
        int id
        int task_id
        int user_id
        text content
        datetime created_at
        datetime updated_at
    }
    e_custom_schedules {
        int id
        text working_days
        string start_time
        string end_time
    }
    e_label_locales {
        int id
        int label_id
        string title
        string locale
    }
    e_labels {
        int id
        string function
        tinyint system
        datetime created_at
        datetime updated_at
    }
    e_milestones {
        int id
        int user_id
        string title
        text description
        date starts_at
        date ends_at
        datetime created_at
        datetime updated_at
    }
    e_task_labels {
        int task_id
        int label_id
    }
    e_tasks {
        int id
        int parent_id
        int creator_id
        int milestone_id
        int estimated_time
        string title
        text description
        datetime created_at
        datetime updated_at
    }
    e_workflows {
        int id
        tinyint system
        text label_ids
    }
    lc_contacts ||--o{ e_assignees : ""
    e_tasks ||--o{ e_assignees : ""
    e_attachments ||--o{ e_attachment_versions : ""
    e_tasks ||--o{ e_attachments : ""
    s_users ||--o{ e_attachments : ""
    e_tasks ||--o{ e_comments : ""
    s_users ||--o{ e_comments : ""
    lc_contacts ||--o{ e_custom_schedules : ""
    e_labels ||--o{ e_label_locales : ""
    s_users ||--o{ e_milestones : ""
    e_labels ||--o{ e_task_labels : ""
    e_tasks ||--o{ e_task_labels : ""
    s_users ||--o{ e_tasks : ""
    e_milestones ||--o{ e_tasks : ""
    e_tasks ||--o{ e_tasks : ""
    e_labels ||--o{ e_workflows : ""

```
