# Emerald Database Schema

```mermaid
erDiagram
    e_milestones {
        string id
        string user_id
        string title
        string description
        string starts_at
        string ends_at
    }
    e_tasks {
        string id
        string parent_id
        string creator_id
        string milestone_id
        string estimated_time
        string title
        string description
    }
    e_attachments {
        string id
        string user_id
        string task_id
        string filename
    }
    e_attachment_versions {
        string id
        string attachment_id
        string created_at
    }
    e_comments {
        string id
        string task_id
        string user_id
        string content
    }
    e_assignees {
        string contact_id
        string task_id
    }
    e_labels {
        string id
        string function
        string system
    }
    e_label_locales {
        string id
        string label_id
        string title
        string locale
    }
    e_task_labels {
        string task_id
        string label_id
    }
    e_workflows {
        string id
        string system
        string label_ids
    }
    e_custom_schedules {
        string id
        string working_days
        string start_time
        string end_time
    }
    e_milestones ||--o{ s_users : ""
    e_tasks ||--o{ e_tasks : ""
    e_tasks ||--o{ s_users : ""
    e_tasks ||--o{ e_milestones : ""
    e_attachments ||--o{ s_users : ""
    e_attachments ||--o{ e_tasks : ""
    e_attachment_versions ||--o{ e_attachments : ""
    e_comments ||--o{ e_tasks : ""
    e_comments ||--o{ s_users : ""
    e_assignees ||--o{ lc_contacts : ""
    e_assignees ||--o{ e_tasks : ""
    e_label_locales ||--o{ e_labels : ""
    e_task_labels ||--o{ e_tasks : ""
    e_task_labels ||--o{ e_labels : ""
    e_workflows ||--o{ e_labels : ""
    e_custom_schedules ||--o{ lc_contacts : ""

```
