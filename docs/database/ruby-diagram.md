# Ruby Database Schema

```mermaid
erDiagram
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

```
