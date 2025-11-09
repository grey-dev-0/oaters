# Complete Database Schema

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
    la_articles {
        int id
        tinyint type
    }
    la_article_locales {
        int id
        int article_id
        string title
        text description
        string locale
    }
    la_categories {
        int id
    }
    la_category_locales {
        int id
        int category_id
        string title
        text description
        string locale
    }
    la_article_categories {
        int article_id
        int category_id
    }
    la_properties {
        int id
        tinyint type
        boolean public
        boolean system
    }
    la_property_locales {
        int id
        int property_id
        string name
        string locale
    }
    la_options {
        int id
    }
    la_option_locales {
        int id
        int option_id
        string name
        string locale
    }
    la_property_options {
        int property_id
        int option_id
    }
    le_bank_accounts {
        int id
        int contact_id
        string bank
        string name
        string number
        string iban
        string swift
    }
    le_orders {
        int id
        int contact_id
        int shipping_address_id
        int billing_address_id
        float amount
        boolean paid
        datetime delivered_at
    }
    le_order_lines {
        int id
        int order_id
        int article_id
        float quantity
        string quantity_unit
        float price
        float discount
        string discount_type
    }
    le_refunds {
        int id
        int refundable_id
        string refundable_type
        float amount
        boolean paid
        datetime returned_at
    }
    le_order_histories {
        int id
        int order_id
        text event
        datetime time
        datetime created_at
    }
    le_transactions {
        int id
        int transferable_id
        string transferable_type
        string type
        float amount
        datetime created_at
    }
    lc_countries {
        int id
        int currency_id
        string code
        boolean status
    }
    lc_country_locales {
        int id
        int country_id
        string name
        string locale
    }
    lc_cities {
        int id
        int country_id
        boolean status
    }
    lc_city_locales {
        int id
        int city_id
        string name
        string locale
    }
    lc_timezones {
        int id
        string identifier
    }
    lc_country_timezones {
        int country_id
        int timezone_id
    }
    lc_contacts {
        int id
        int user_id
        int timezone_id
        string name
        string job
        string image
        string gender
        tinyint marital_status
        date birthdate
    }
    lc_phones {
        int id
        int contact_id
        string number
        boolean default
    }
    lc_emails {
        int id
        int contact_id
        string address
        boolean default
    }
    lc_addresses {
        int id
        int contact_id
        int country_id
        boolean default
    }
    lc_colors {
        string id
    }
    lc_color_locales {
        int id
        string color_id
        string name
        string locale
    }
    e_milestones {
        int id
        int user_id
        string title
        text description
        date starts_at
        date ends_at
    }
    e_tasks {
        int id
        int parent_id
        int creator_id
        int milestone_id
        int estimated_time
        string title
        text description
    }
    e_attachments {
        int id
        int user_id
        int task_id
        string filename
    }
    e_attachment_versions {
        int id
        int attachment_id
        datetime created_at
    }
    e_comments {
        int id
        int task_id
        int user_id
        text content
    }
    e_assignees {
        int contact_id
        int task_id
    }
    e_labels {
        int id
        string function
        boolean system
    }
    e_label_locales {
        int id
        int label_id
        string title
        string locale
    }
    e_task_labels {
        int task_id
        int label_id
    }
    e_workflows {
        int id
        boolean system
        text label_ids
    }
    e_custom_schedules {
        int id
        text working_days
        string start_time
        string end_time
    }
    o_purchases {
        int id
        int contact_id
        int bank_account_id
        float amount
        boolean paid
        datetime received_at
    }
    o_purchase_lines {
        int id
        int purchase_id
        int article_id
        float quantity
        string quantity_unit
        float price
    }
    o_purchase_histories {
        int id
        int purchase_id
        text event
        datetime time
        datetime created_at
    }
    o_product_plans {
        int id
        int user_id
        int article_id
        float quantity
        string quantity_unit
    }
    o_plan_consumptions {
        int id
        int product_plan_id
        int article_id
        float quantity
        string quantity_unit
    }
    o_plan_executions {
        int id
        int user_id
        int product_plan_id
        string plan_log_id
        tinyint status
        text note
    }
    o_execution_consumptions {
        int id
        int plan_execution_id
        int article_id
        float quantity
        string quantity_unit
    }
    o_execution_logs {
        int id
        int user_id
        int plan_execution_id
        tinyint status
        text note
        datetime created_at
    }
    r_salaries {
        int id
        string type
    }
    r_payroll_components {
        int id
        string type
    }
    r_payroll_component_locales {
        int id
        int payroll_component_id
        string title
        string locale
    }
    r_salary_components {
        int salary_id
        int payroll_component_id
        float amount
    }
    r_payroll_payments {
        int id
        int salary_id
        int bank_account_id
        tinyint units
        datetime paid_at
    }
    r_payment_components {
        int payroll_payment_id
        int payroll_component_id
        float amount
    }
    r_notices {
        int id
        int author_id
        int contact_id
        int payroll_payment_id
        string type
        text content
    }
    r_leaves {
        int id
        int contact_id
        string type
        boolean status
        date starts_at
        date ends_at
    }
    r_departments {
        int id
    }
    r_department_locales {
        int id
        int department_id
        string locale
        string name
    }
    r_subordinates {
        string id
        int manager_id
        int contact_id
        int department_id
    }
    r_punches {
        bigint id
        int contact_id
        string type
        datetime created_at
    }
    r_vacancies {
        int id
        int department_id
        boolean active
    }
    r_vacancy_locales {
        int id
        int vacancy_id
        string locale
        string title
        text description
    }
    r_degrees {
        int id
    }
    r_degree_locales {
        int id
        int degree_id
        string locale
        string name
    }
    r_applicants {
        int id
        int country_id
        int degree_id
        int degree_date
        tinyint tenure
        datetime recruited_at
    }
    r_applicables {
        int applicant_id
        string applicable
    }
    r_documents {
        int id
        int applicant_id
        string title
        string filename
    }
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
    tenants {
        int id
        int user_id
        string name
        string email
        string password
        string hash
        text data
    }
    domains {
        int id
        string domain
        int tenant_id
    }
    subscriptions {
        int id
        int tenant_id
        float price
        int discount
        string discount_type
        boolean paid
        datetime expires_at
    }
    modules {
        int id
        string name
        float price
    }
    tenant_modules {
        int subscription_id
        int module_id
    }
    purchases {
        int id
        int subscription_id
        float amount
        string token
        boolean executed
    }
    a_carts ||--o{ s_users : ""
    a_cart_articles ||--o{ a_carts : ""
    a_cart_articles ||--o{ la_articles : ""
    la_article_locales ||--o{ la_articles : ""
    la_category_locales ||--o{ la_categories : ""
    la_article_categories ||--o{ la_articles : ""
    la_article_categories ||--o{ la_categories : ""
    la_property_locales ||--o{ la_properties : ""
    la_option_locales ||--o{ la_options : ""
    la_property_options ||--o{ la_properties : ""
    la_property_options ||--o{ la_options : ""
    le_bank_accounts ||--o{ lc_contacts : ""
    le_orders ||--o{ lc_contacts : ""
    le_orders ||--o{ lc_addresses : ""
    le_order_lines ||--o{ le_orders : ""
    le_order_lines ||--o{ la_articles : ""
    le_order_histories ||--o{ le_orders : ""
    lc_countries ||--o{ le_currencies : ""
    lc_country_locales ||--o{ lc_countries : ""
    lc_cities ||--o{ lc_countries : ""
    lc_city_locales ||--o{ lc_cities : ""
    lc_country_timezones ||--o{ lc_countries : ""
    lc_country_timezones ||--o{ lc_timezones : ""
    lc_contacts ||--o{ s_users : ""
    lc_contacts ||--o{ lc_timezones : ""
    lc_phones ||--o{ lc_contacts : ""
    lc_emails ||--o{ lc_contacts : ""
    lc_addresses ||--o{ lc_contacts : ""
    lc_addresses ||--o{ lc_countries : ""
    lc_color_locales ||--o{ lc_colors : ""
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
    o_purchases ||--o{ lc_contacts : ""
    o_purchases ||--o{ le_bank_accounts : ""
    o_purchase_lines ||--o{ o_purchases : ""
    o_purchase_lines ||--o{ la_articles : ""
    o_purchase_histories ||--o{ o_purchases : ""
    o_product_plans ||--o{ s_users : ""
    o_product_plans ||--o{ la_articles : ""
    o_plan_consumptions ||--o{ o_product_plans : ""
    o_plan_consumptions ||--o{ la_articles : ""
    o_plan_executions ||--o{ s_users : ""
    o_plan_executions ||--o{ o_product_plans : ""
    o_execution_consumptions ||--o{ o_plan_executions : ""
    o_execution_consumptions ||--o{ la_articles : ""
    o_execution_logs ||--o{ s_users : ""
    o_execution_logs ||--o{ o_plan_executions : ""
    r_salaries ||--o{ lc_contacts : ""
    r_payroll_component_locales ||--o{ r_payroll_components : ""
    r_salary_components ||--o{ r_salaries : ""
    r_salary_components ||--o{ r_payroll_components : ""
    r_payroll_payments ||--o{ r_salaries : ""
    r_payroll_payments ||--o{ le_bank_accounts : ""
    r_notices ||--o{ lc_contacts : ""
    r_notices ||--o{ r_payroll_payments : ""
    r_leaves ||--o{ lc_contacts : ""
    r_department_locales ||--o{ r_departments : ""
    r_subordinates ||--o{ lc_contacts : ""
    r_subordinates ||--o{ r_departments : ""
    r_punches ||--o{ lc_contacts : ""
    r_vacancies ||--o{ r_departments : ""
    r_vacancy_locales ||--o{ r_vacancies : ""
    r_degree_locales ||--o{ r_degrees : ""
    r_applicants ||--o{ lc_contacts : ""
    r_applicants ||--o{ lc_countries : ""
    r_applicants ||--o{ r_degrees : ""
    r_applicables ||--o{ r_applicants : ""
    r_documents ||--o{ r_applicants : ""
    s_role_locales ||--o{ s_roles : ""
    s_model_has_permissions ||--o{ s_permissions : ""
    s_model_has_roles ||--o{ s_roles : ""
    s_role_has_permissions ||--o{ s_permissions : ""
    s_role_has_permissions ||--o{ s_roles : ""
    domains ||--o{ tenants : ""
    subscriptions ||--o{ tenants : ""
    tenant_modules ||--o{ subscriptions : ""
    tenant_modules ||--o{ modules : ""
    purchases ||--o{ subscriptions : ""

```
