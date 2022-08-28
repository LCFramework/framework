import{_ as s,c as a,o as e,a as o}from"./app.ddf9044d.js";const F=JSON.parse('{"title":"Building Modules","description":"","frontmatter":{},"headers":[{"level":2,"title":"Manifest","slug":"manifest","link":"#manifest","children":[{"level":3,"title":"The basics","slug":"the-basics","link":"#the-basics","children":[]}]},{"level":2,"title":"Service providers","slug":"service-providers","link":"#service-providers","children":[]}],"relativePath":"docs/modules/building-modules.md","lastUpdated":1661658362000}'),n={name:"docs/modules/building-modules.md"},l=o(`<h1 id="building-modules" tabindex="-1">Building Modules <a class="header-anchor" href="#building-modules" aria-hidden="true">#</a></h1><p>Modules in this definition are essentially packages of code written in PHP to extend or customise functionality.</p><div class="warning custom-block"><p class="custom-block-title">WARNING</p><p>LCFramework is currently in <code>alpha</code> status. This means there may be bugs, and the API may still change between minor versions.</p></div><h2 id="manifest" tabindex="-1">Manifest <a class="header-anchor" href="#manifest" aria-hidden="true">#</a></h2><p>LCFramework requires modules to have a manifest file that provides common information to the framework that is uses for administration, dependency management, and booting your module.</p><p>Since LCFramework is built on modern PHP, LCFramework utilises Composer and thus the <code>composer.json</code> file as your manifest file. This saves you from having to write both a manifest file for Composer to manage to dependencies and a separate manifest file for your module.</p><h3 id="the-basics" tabindex="-1">The basics <a class="header-anchor" href="#the-basics" aria-hidden="true">#</a></h3><p>Your manifest file must contain at least the following, everything else is completely your choice:</p><ul><li>Name</li><li>Description</li><li>Version</li><li>LCFramework module definition</li></ul><p>You may use the below example as a starting point for your module:</p><div class="language-json"><button class="copy"></button><span class="lang">json</span><pre><code><span class="line"><span style="color:#89DDFF;">{</span></span>
<span class="line"><span style="color:#A6ACCD;">    </span><span style="color:#89DDFF;">&quot;</span><span style="color:#C792EA;">name</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">:</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">your-name/module-name</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">,</span></span>
<span class="line"><span style="color:#A6ACCD;">    </span><span style="color:#89DDFF;">&quot;</span><span style="color:#C792EA;">description</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">:</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">Build something great!</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">,</span></span>
<span class="line"><span style="color:#A6ACCD;">    </span><span style="color:#89DDFF;">&quot;</span><span style="color:#C792EA;">version</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">:</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">1.0</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">,</span></span>
<span class="line"><span style="color:#A6ACCD;">    </span><span style="color:#89DDFF;">&quot;</span><span style="color:#C792EA;">extra</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">:</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">{</span></span>
<span class="line"><span style="color:#A6ACCD;">        </span><span style="color:#89DDFF;">&quot;</span><span style="color:#FFCB6B;">lcframework</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">:</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">{</span></span>
<span class="line"><span style="color:#A6ACCD;">            </span><span style="color:#89DDFF;">&quot;</span><span style="color:#F78C6C;">module</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">:</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">{</span></span>
<span class="line"><span style="color:#A6ACCD;">                </span><span style="color:#89DDFF;">&quot;</span><span style="color:#F07178;">providers</span><span style="color:#89DDFF;">&quot;</span><span style="color:#89DDFF;">:</span><span style="color:#A6ACCD;"> </span><span style="color:#89DDFF;">[</span><span style="color:#A6ACCD;">   </span></span>
<span class="line"><span style="color:#A6ACCD;">                    </span><span style="color:#89DDFF;">&quot;</span><span style="color:#C3E88D;">YourName</span><span style="color:#A6ACCD;">\\\\</span><span style="color:#C3E88D;">ModuleName</span><span style="color:#A6ACCD;">\\\\</span><span style="color:#C3E88D;">ModuleNameServiceProvider</span><span style="color:#89DDFF;">&quot;</span></span>
<span class="line"><span style="color:#A6ACCD;">                </span><span style="color:#89DDFF;">]</span></span>
<span class="line"><span style="color:#A6ACCD;">            </span><span style="color:#89DDFF;">}</span></span>
<span class="line"><span style="color:#A6ACCD;">        </span><span style="color:#89DDFF;">}</span></span>
<span class="line"><span style="color:#A6ACCD;">    </span><span style="color:#89DDFF;">}</span></span>
<span class="line"><span style="color:#89DDFF;">}</span></span>
<span class="line"></span></code></pre></div><div class="tip custom-block"><p class="custom-block-title">TIP</p><p>LCFramework will automatically include the Composer autoloader by your module, and boot any <a href="#service-providers">Service Providers</a> defined in your manifest.</p></div><h2 id="service-providers" tabindex="-1">Service providers <a class="header-anchor" href="#service-providers" aria-hidden="true">#</a></h2><p>Service providers are the entry-point into your module. You can have as many service providers as you wish, and LCFramework will automatically boot them when the module is enabled. To learn more about Service Providers, you can use the <a href="https://laravel.com/docs/providers" target="_blank" rel="noreferrer">Laravel documentation</a>.</p><p>Service providers must be defined in your Composer file, using the full namespace and classname.</p>`,15),p=[l];function t(r,i,c,d,D,u){return e(),a("div",null,p)}const m=s(n,[["render",t]]);export{F as __pageData,m as default};
