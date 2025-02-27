---
# FleetPay API

**FleetPay API** is a robust backend service designed to manage ride-sharing fleet operations, earnings, and driver payments.

---

## Overview

The API provides endpoints to manage:

- **Platform Earnings**: Import and track earnings data from multiple ride-sharing services (e.g., Uber, Bolt, Heetch)
- **Driver Profiles & Earnings**: Manage driver details and monitor individual earnings
- **Commission Settings**: Configure and calculate commissions
- **Payment Tracking**: Track and report payment statuses

---

## Core Features

### 1. Platform Earnings Management
- **Data Import:** Import earnings data from various ride-sharing platforms.
- **Weekly Tracking:** Monitor weekly earnings per driver.
- **Record Management:** Validate and manage earning records.
- **Commission Calculation:** Automatically calculate commissions and due amounts.

### 2. Driver Management
- **Profile Management:** Create and update driver profiles.
- **Earnings Tracking:** Record and review individual driver earnings.
- **Performance Monitoring:** Assess driver performance across platforms.

### 3. Commission System
- **Custom Rates:** Set user-specific commission rates.
- **Automated Calculations:** Handle commission computations automatically.
- **Flexible Options:** Easily adjust commission settings as needed.

### 4. Reporting
- **Weekly Reports:** Generate detailed weekly earnings reports.
- **Platform Insights:** Analyze performance data by platform.
- **Payment Monitoring:** Keep track of payment statuses.

---

## API Endpoints

### Platform Earnings
- **List Earnings:**  
  `GET /api/platform-earnings`  
  _Retrieve all platform earnings records._

- **Create Record:**  
  `POST /api/platform-earnings`  
  _Create a new earning record._

- **Update Record:**  
  `PUT /api/platform-earnings/{id}`  
  _Update an existing earning record._

- **Delete Record:**  
  `DELETE /api/platform-earnings/{id}`  
  _Delete an earning record._

### Reports
- **Import Status:**  
  `GET /api/reports/platforms/import/status/{weekStartDate}`  
  _Get the import status for a given week._

- **Import Platform Data:**  
  `POST /api/reports/platforms/import/{platform}`  
  _Initiate import of platform data._

- **Delete Platform Data:**  
  `DELETE /api/reports/platforms/import/{platform}/{weekStartDate}`  
  _Remove platform data for the specified week._

### Settings
- **Get Commission Settings:**  
  `GET /api/settings/commission`  
  _Retrieve current commission settings._

- **Update Commission Settings:**  
  `POST /api/settings/commission`  
  _Modify commission settings._

---

## Authentication

The API uses **Sanctum** for authentication _(currently commented out for development)_.

---

## Error Handling

All endpoints return appropriate HTTP status codes and error messages in **JSON** format.

---
