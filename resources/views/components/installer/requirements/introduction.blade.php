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
            :installed="$this->extensions['php']"
        >
            PHP >= 8.0 (installed: {{phpversion()}})
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$this->extensions['bcmath']"
        >
            BCMath PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$this->extensions['ctype']"
        >
            Ctype PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$this->extensions['curl']"
        >
            cURL PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$this->extensions['dom']"
        >
            DOM PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$this->extensions['fileinfo']"
        >
            Fileinfo PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$this->extensions['json']"
        >
            JSON PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$this->extensions['mbstring']"
        >
            Mbstring PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$this->extensions['openssl']"
        >
            OpenSSL PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$this->extensions['pcre']"
        >
            PCRE PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$this->extensions['PDO']"
        >
            PDO PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$this->extensions['pdo_mysql']"
        >
            PDO MySQL
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$this->extensions['pdo_sqlite']"
        >
            PDO SQLite
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$this->extensions['tokenizer']"
        >
            Tokenizer PHP Extension
        </x-lcframework::installer.requirements.item>

        <x-lcframework::installer.requirements.item
            :installed="$this->extensions['xml']"
        >
            XML PHP Extension
        </x-lcframework::installer.requirements.item>
    </ul>
</div>
