# Brigmaster Core

## Description

WordPress plugin that provides construction material calculators and exposes a REST API for estimate requests.

## Installation Requirements

- PHP 8.2+
- WordPress
- Composer

## Installation

```bash
composer install
```

Activate the `brigmaster-core` plugin in the WordPress admin panel after dependencies are installed.

## REST Endpoint

- Method: `POST`
- URL: `/wp-json/brigmaster/v1/estimate`
- Content-Type: `application/json`

## Example Request

```bash
curl -X POST "http://YOUR_SITE/wp-json/brigmaster/v1/estimate" \
  -H "Content-Type: application/json" \
  -d '{
    "calculator": "slab_foundation",
    "mode": "dimensions",
    "length": 10,
    "width": 8,
    "height": 0.25,
    "includeReinforcement": true,
    "includeFormwork": true
  }'
```

## Example Success Response

```json
{
  "calculator": "slab_foundation",
  "mode": "dimensions",
  "concrete": {
    "areaM2": 80,
    "heightM": 0.25,
    "volumeM3": 20
  },
  "reinforcement": {
    "diameterMm": 12,
    "stepMm": 200,
    "layers": 2,
    "reservePercent": 10,
    "barsAlongLength": 41,
    "barsAlongWidth": 51,
    "totalLengthM": 1636,
    "totalLengthWithReserveM": 1799.6,
    "unitWeightKgPerM": 0.89,
    "massKg": 1599.64
  },
  "formwork": {
    "heightM": 0.3,
    "reservePercent": 10,
    "perimeterM": 36,
    "areaM2": 11.88,
    "linearMeters": 39.6
  }
}
```

## Example Validation Error Response

```json
{
  "code": "validation_error",
  "message": "Validation failed.",
  "errors": {
    "length": [
      "The length field is required and must be greater than 0 when includeReinforcement/includeFormwork is true and mode is area."
    ],
    "width": [
      "The width field is required and must be greater than 0 when includeReinforcement/includeFormwork is true and mode is area."
    ]
  }
}
```

## CLI Examples

```bash
php examples/estimate_cli.php
php examples/smoke_check.php
```
