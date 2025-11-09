# Sapphire Database Schema

```mermaid
erDiagram
    s_users {
        int id
        string username
        string password
        string image
    }
    s_permissions {
        bigint id
        string name
        string guard_name
    }
    s_roles {
        bigint id
        string name
        string guard_name
    }
    s_role_locales {
        bigint id
        bigint role_id
        string title
        string locale
    }
    s_model_has_permissions {
        bigint permission_id
        bigint model_id
        string model_type
    }
    s_model_has_roles {
        bigint role_id
        bigint model_id
        string model_type
    }
    s_role_has_permissions {
        bigint permission_id
        bigint role_id
    }
    s_role_locales ||--o{ s_roles : ""
    s_model_has_permissions ||--o{ s_permissions : ""
    s_model_has_roles ||--o{ s_roles : ""
    s_role_has_permissions ||--o{ s_permissions : ""
    s_role_has_permissions ||--o{ s_roles : ""

```
