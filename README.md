# Sms Manager for Filament PHP

A simple Filament plugin to add SMS statistics and management widgets to your dashboard.

## Installation

Install via composer:

```bash
composer require mortezaa97/sms-manager
```

## Usage

1. **Register the Plugin**

In your `AdminPanelServiceProvider.php`, register the plugin in the plugins array:

```php
use Mortezaa97\SmsManager\SmsManagerPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            // ... other plugins
            SmsManagerPlugin::make(),
        ]);
}
```

2. **Add the Widget to Your Dashboard**

In `app/Filament/Pages/Dashboard.php`, add the widget to the `getWidgets()` method:

```php
public function getWidgets(): array
{
    return [
        // ... other widgets
        \Mortezaa97\SmsManager\Filament\Widgets\SmsManagerStatsWidget::class,
    ];
}
```

## Features

- Shows SMS statistics on your Filament dashboard.
- Easy integration with existing Filament panels and widgets.

## Customization

You can customize or extend the widget by creating your own widget or forking this package.

## Credits

Created by [mortezaa97](https://github.com/mortezaa97)

## License

MIT
