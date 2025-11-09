# Complete Database Schema

```mermaid
erDiagram
    a_carts {
        string id
        string user_id
        string token
    }
    a_cart_articles {
        string cart_id
        string article_id
        string quantity
        string quantity_unit
    }
    la_articles {
        string id
        string type
    }
    la_article_locales {
        string id
        string article_id
        string title
        string description
        string locale
    }
    la_categories {
        string id
    }
    la_category_locales {
        string id
        string category_id
        string title
        string description
        string locale
    }
    la_article_categories {
        string article_id
        string category_id
    }
    la_properties {
        string id
        string type
        string public
        string system
    }
    la_property_locales {
        string id
        string property_id
        string name
        string locale
    }
    la_options {
        string id
    }
    la_option_locales {
        string id
        string option_id
        string name
        string locale
    }
    la_property_options {
        string property_id
        string option_id
    }
    le_bank_accounts {
        string id
        string contact_id
        string bank
        string name
        string number
        string iban
        string swift
    }
    le_orders {
        string id
        string contact_id
        string shipping_address_id
        string billing_address_id
        string amount
        string paid
        string delivered_at
    }
    le_order_lines {
        string id
        string order_id
        string article_id
        string quantity
        string quantity_unit
        string price
        string discount
        string discount_type
    }
    le_refunds {
        string id
        string refundable_id
        string refundable_type
        string amount
        string paid
        string returned_at
    }
    le_order_histories {
        string id
        string order_id
        string event
        string time
        string created_at
    }
    le_transactions {
        string id
        string transferable_id
        string transferable_type
        string type
        string amount
        string created_at
    }
    lc_countries {
        string id
        string currency_id
        string code
        string status
    }
    lc_country_locales {
        string id
        string country_id
        string name
        string locale
    }
    lc_cities {
        string id
        string country_id
        string status
    }
    lc_city_locales {
        string id
        string city_id
        string name
        string locale
    }
    lc_timezones {
        string id
        string identifier
    }
    lc_country_timezones {
        string country_id
        string timezone_id
    }
    lc_contacts {
        string id
        string user_id
        string timezone_id
        string name
        string job
        string image
        string gender
        string marital_status
        string birthdate
    }
    lc_phones {
        string id
        string contact_id
        string number
        string default
    }
    lc_emails {
        string id
        string contact_id
        string address
        string default
    }
    lc_addresses {
        string id
        string contact_id
        string country_id
        string default
    }
    lc_colors {
        string id
    }
    lc_color_locales {
        string id
        string color_id
        string name
        string locale
    }
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
    o_purchases {
        string id
        string contact_id
        string bank_account_id
        string amount
        string paid
        string received_at
    }
    o_purchase_lines {
        string id
        string purchase_id
        string article_id
        string quantity
        string quantity_unit
        string price
    }
    o_purchase_histories {
        string id
        string purchase_id
        string event
        string time
        string created_at
    }
    o_product_plans {
        string id
        string user_id
        string article_id
        string quantity
        string quantity_unit
    }
    o_plan_consumptions {
        string id
        string product_plan_id
        string article_id
        string quantity
        string quantity_unit
    }
    o_plan_executions {
        string id
        string user_id
        string product_plan_id
        string plan_log_id
        string status
        string note
    }
    o_execution_consumptions {
        string id
        string plan_execution_id
        string article_id
        string quantity
        string quantity_unit
    }
    o_execution_logs {
        string id
        string user_id
        string plan_execution_id
        string status
        string note
        string created_at
    }
    r_salaries {
        string id
        string type
    }
    r_payroll_components {
        string id
        string type
    }
    r_payroll_component_locales {
        string id
        string payroll_component_id
        string title
        string locale
    }
    r_salary_components {
        string salary_id
        string payroll_component_id
        string amount
    }
    r_payroll_payments {
        string id
        string salary_id
        string bank_account_id
        string units
        string paid_at
    }
    r_payment_components {
        string payroll_payment_id
        string payroll_component_id
        string amount
    }
    r_notices {
        string id
        string author_id
        string contact_id
        string payroll_payment_id
        string type
        string content
    }
    r_leaves {
        string id
        string contact_id
        string type
        string status
        string starts_at
        string ends_at
    }
    r_departments {
        string id
    }
    r_department_locales {
        string id
        string department_id
        string locale
        string name
    }
    r_subordinates {
        string id
        string manager_id
        string contact_id
        string department_id
    }
    r_punches {
        string id
        string contact_id
        string type
        string created_at
    }
    r_vacancies {
        string id
        string department_id
        string active
    }
    r_vacancy_locales {
        string id
        string vacancy_id
        string locale
        string title
        string description
    }
    r_degrees {
        string id
    }
    r_degree_locales {
        string id
        string degree_id
        string locale
        string name
    }
    r_applicants {
        string id
        string country_id
        string degree_id
        string degree_date
        string tenure
        string recruited_at
    }
    r_applicables {
        string applicant_id
        string applicable
    }
    r_documents {
        string id
        string applicant_id
        string title
        string filename
    }
    s_users {
        string id
        string username
        string password
        string image
    }
    s_permissions {
        string id
        string name
        string guard_name
    }
    s_roles {
        string id
        string name
        string guard_name
    }
    s_role_locales {
        string id
        string role_id
        string title
        string locale
    }
    s_model_has_permissions {
        string permission_id
        string model_id
        string model_type
    }
    s_model_has_roles {
        string role_id
        string model_id
        string model_type
    }
    s_role_has_permissions {
        string permission_id
        string role_id
    }
    tenants {
        string id
        string user_id
        string name
        string email
        string password
        string hash
        string data
    }
    domains {
        string id
        string domain
        string tenant_id
    }
    subscriptions {
        string id
        string tenant_id
        string price
        string discount
        string discount_type
        string paid
        string expires_at
    }
    modules {
        string id
        string name
        string price
    }
    tenant_modules {
        string subscription_id
        string module_id
    }
    purchases {
        string id
        string subscription_id
        string amount
        string token
        string executed
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
