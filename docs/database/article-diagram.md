# Lava Article Database Schema

```mermaid
erDiagram
    la_article_categories {
        int article_id
        int category_id
    }
    la_article_locales {
        int id
        int article_id
        string title
        text description
        string locale
    }
    la_articles {
        int id
        tinyint type
        datetime created_at
        datetime updated_at
    }
    la_categories {
        int id
        datetime created_at
        datetime updated_at
    }
    la_category_locales {
        int id
        int category_id
        string title
        text description
        string locale
    }
    la_option_locales {
        int id
        int option_id
        string name
        string locale
    }
    la_options {
        int id
    }
    la_properties {
        int id
        tinyint type
        tinyint public
        tinyint system
    }
    la_property_locales {
        int id
        int property_id
        string name
        string locale
    }
    la_property_options {
        int property_id
        int option_id
    }
    la_articles ||--o{ la_article_categories : ""
    la_categories ||--o{ la_article_categories : ""
    la_articles ||--o{ la_article_locales : ""
    la_categories ||--o{ la_category_locales : ""
    la_options ||--o{ la_option_locales : ""
    la_properties ||--o{ la_property_locales : ""
    la_options ||--o{ la_property_options : ""
    la_properties ||--o{ la_property_options : ""

```
