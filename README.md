# FleetPay API

FleetPay API is a robust backend service designed to manage ride-sharing fleet operations, earnings, and driver payments.

## Overview

The API provides endpoints to manage:
- Platform earnings from multiple ride-sharing services (Uber, Bolt, Heetch)
- Driver profiles and earnings
- Commission settings and calculations
- Payment tracking and reporting

## Core Features

### Platform Earnings Management
- Import earnings data from multiple ride-sharing platforms
- Track weekly earnings per driver
- Validate and manage earning records
- Calculate commissions and due amounts

### Driver Management
- Create and manage driver profiles
- Track individual driver earnings
- Monitor performance across platforms

### Commission System
- User-specific commission rates
- Automatic commission calculations
- Flexible commission management

### Reporting
- Weekly earnings reports
- Platform-specific performance tracking
- Payment status monitoring

## API Endpoints

### Platform Earnings
- `GET /api/platform-earnings` - List all platform earnings
- `POST /api/platform-earnings` - Create new earning record
- `PUT /api/platform-earnings/{id}` - Update earning record
- `DELETE /api/platform-earnings/{id}` - Delete earning record

### Reports
- `GET /api/reports/platforms/import/status/{weekStartDate}` - Get import status
- `POST /api/reports/platforms/import/{platform}` - Import platform data
- `DELETE /api/reports/platforms/import/{platform}/{weekStartDate}` - Delete platform data

### Settings
- `GET /api/settings/commission` - Get commission settings
- `POST /api/settings/commission` - Update commission settings

## Authentication

The API uses Sanctum for authentication (currently commented out for development).

## Error Handling

All endpoints return appropriate HTTP status codes and error messages in JSON format.
