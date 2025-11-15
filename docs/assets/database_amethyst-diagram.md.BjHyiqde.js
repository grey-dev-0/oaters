import{_ as s,c as i,o as t,a5 as n}from"./chunks/framework.MXlVYeMd.js";const k=JSON.parse('{"title":"Amethyst Database Schema","description":"","frontmatter":{},"headers":[],"relativePath":"database/amethyst-diagram.md","filePath":"database/amethyst-diagram.md"}'),e={name:"database/amethyst-diagram.md"};function l(p,a,h,E,r,c){return t(),i("div",null,a[0]||(a[0]=[n(`<h1 id="amethyst-database-schema" tabindex="-1">Amethyst Database Schema <a class="header-anchor" href="#amethyst-database-schema" aria-label="Permalink to &quot;Amethyst Database Schema&quot;">​</a></h1><div class="language-mermaid vp-adaptive-theme"><button title="Copy Code" class="copy"></button><span class="lang">mermaid</span><pre class="shiki shiki-themes github-light github-dark vp-code" tabindex="0"><code><span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">erDiagram</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">    a_cart_articles {</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">        int cart_id</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">        int article_id</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">        double quantity</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">        int quantity_unit_id</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">    }</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">    a_carts {</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">        int id</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">        int user_id</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">        string token</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">        datetime created_at</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">        datetime updated_at</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">    }</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">    la_articles ||--o{ a_cart_articles : &quot;&quot;</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">    a_carts ||--o{ a_cart_articles : &quot;&quot;</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">    lc_measurement_units ||--o{ a_cart_articles : &quot;&quot;</span></span>
<span class="line"><span style="--shiki-light:#24292E;--shiki-dark:#E1E4E8;">    s_users ||--o{ a_carts : &quot;&quot;</span></span></code></pre></div>`,2)]))}const o=s(e,[["render",l]]);export{k as __pageData,o as default};
