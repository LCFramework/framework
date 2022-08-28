# Getting Started

Modules allow website administrators to easily extend the functionality of their application.
Modules can either extend the LCFramework core, or build upon it (or both!). This allows each
installation to not only share functionality, but even customise their website to make it unique.

::: warning
LCFramework is currently in `alpha` status. This means there may be bugs,
and the API may still change between minor versions.
:::

## Installing modules

Modules must be compressed into a zip file, allowing for efficient uploading and downloading to
and from servers.

The [Administration panel](#) provides the capability to install modules out of box via the
`Modules` page. Once you've chosen a module that you wish to install onto your website, follow
the steps outlined below:

1. Go to the `Modules` page in the [Administration panel](#) of your website. The url is likely
similar to `https://yourdomain.com/admin/extend/modules`.
2. At the top of the page, you should see a button labelled "Install modules". Clicking this button
will prompt you with a modal that allows you to select the modules you wish to install. You can
install more than one module at a time. Simply either drag-and-drop the module zip file you
downloaded, or click to browse your filesystem.
3. Once you've uploaded the modules you wish to install, click the "Submit" button at the bottom
of the modal, and LCFramework will automatically unpack the modules you uploaded and install it for you.
4. LCFramework will notify you once all the modules you just uploaded have been installed. By
default, modules are disabled. You are required to specify what modules you want enabled.
5. See [Managing modules](#managing-modules) to enable your newly installed modules.

## Managing modules

Modules have at minimum three different states; `Enabled`, `Disabled`, and `Deleted`.
Modules that are in the `Enabled` state will boot alongside your application on every request.
Modules that are `Disabled` are not loaded, and this will not boot.
Modules that are `Deleted` have just been deleted in the current request, and will be
forgotten once the request has been handled. Modules in the `Deleted` state are only for
developers, and are never shown to the end-user.

A list of every module installed can be found on the `Modules` page in the [Administration panel](#) of your website.
This table supports bulk editing, as well as filters (and search) to easily manage what runs
on your website.

## Enabling & disabling modules

Modules can be enabled and disabled using the actions inside the table on the `Modules` page 
in the [Administration panel](#) of your website.

In the table, you'll have the option to enable or disable each module (depending on their state).
In addition to enabling and disabling each module individually, you have the option to select multiple modules
and bulk enable them.
