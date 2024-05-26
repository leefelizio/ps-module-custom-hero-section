{**
 * Copyright  2024 Lee Felizio Castro
 * @author    Lee Felizio Castro <feliziolee@gmail.com>
 * @copyright Since 2024 Lee Felizio Castro
 * @license   https://opensource.org/license/mit MIT LICENSE
 *}
<hr />
<pre>
  /**
  * Copyright 2024 Lee Felizio Castro
  * @author    Lee Felizio Castro <feliziolee@gmail.com>
  * @copyright Since 2024 Lee Felizio Castro
  * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
  */
  
  // Welcome to the Custom Hero Section Prestashop custom module

  // This module has the following data:
  hero_eyebrow: "{$hero_eyebrow}"
  hero_heading: "{$hero_heading}"
  hero_subtitle: "{$hero_subtitle}"
  hero_btn_link: "{$hero_btn_link}"
  hero_btn_text: "{$hero_btn_text}"
  hero_icon: "{$hero_icon}"

  // Please, see below the module displayed:

</pre>
{if isset($hero_icon)}
    <section class="custom-hero-section">
      <div class="hero-container">
        <div class="hero-header">
          <img
            alt="{$hero_heading}"
            class="hero-icon"
            src="{$hero_icon}"
            title="{$hero_heading}"
          /> 
          <p class="eyebrow">
            {$hero_eyebrow}
          </p>
        </div>
        <div class="hero-body">
          <h2>
            {$hero_heading} 
          </h2>
          <h3>
            {$hero_subtitle}
          </h3>
          <a 
            class="hero-btn"
            title="{$hero_heading}"
            href=" {$hero_btn_link}"
          >
            {$hero_btn_text}
          </a>
        </div>
      </div>
    </section>
{else}
      <p>
        Please, add an icon to
        <strong>
          {$hero_heading}
        </strong>
      </p>
{/if}
<hr />
