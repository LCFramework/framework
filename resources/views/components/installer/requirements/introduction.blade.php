<div class="space-y-4">
    <p>
        Welcome to LCFramework. Before getting started, we need some information
        to help us configure your application to fit your requirements.
    </p>

    <p>
        All this information can be easily changed at any time after installation.
    </p>

    <p>
        Before continuing, please ensure your environment meets the requirements:
    </p>

    <ul>
        <x-lcframework::installer.requirements.item
            :installed="$extensions['php']"
        >
            PHP >= 8.0 (installed: {{phpversion()}})
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$extensions['bcmath']"
        >
            BCMath PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$extensions['ctype']"
        >
            Ctype PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$extensions['curl']"
        >
            cURL PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$extensions['dom']"
        >
            DOM PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$extensions['fileinfo']"
        >
            Fileinfo PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$extensions['json']"
        >
            JSON PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$extensions['mbstring']"
        >
            Mbstring PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$extensions['openssl']"
        >
            OpenSSL PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$extensions['pcre']"
        >
            PCRE PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$extensions['pdo']"
        >
            PDO PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$extensions['pdo_mysql']"
        >
            PDO MySQL
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$extensions['pdo_sqlite']"
        >
            PDO SQLite
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$extensions['tokenizer']"
        >
            Tokenizer PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$extensions['xml']"
        >
            XML PHP Extension
        </x-lcframework::installer.requirements.item>
    </ul>
</div>
