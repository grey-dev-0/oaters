# Amethyst Database Schema

```mermaid
erDiagram
    a_carts {
        int id
        int user_id
        string token
    }
    a_cart_articles {
        int cart_id
        int article_id
        float quantity
        string quantity_unit
    }
    a_carts ||--o{ s_users : ""
    a_cart_articles ||--o{ a_carts : ""
    a_cart_articles ||--o{ la_articles : ""

```
