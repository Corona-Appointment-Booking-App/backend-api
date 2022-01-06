# Introduction

users can book appointments for an test center they wish and select a date and time they want to book for

each city (e.g düsseldorf) have city locations (e.g bilk), the test centers are assigned to an city location

the administration will be build in /var/www/backend-api/public/admin

URL: http://corona-api.test/admin

[Installation](documentation/installation.md)

# Overview

the project has three standalone applications

| Description         | Repository URL                                                 | 
|---------------------|----------------------------------------------------------------|
| Symfony Backend API | https://github.com/Corona-Appointment-Booking-App/backend-api  |
| Admin App           | https://github.com/Corona-Appointment-Booking-App/admin-app    |
| Frontend App        | https://github.com/Corona-Appointment-Booking-App/frontend-app |

# Used Dependencies

| Description                                     | Repository URL                                                      | 
|-------------------------------------------------|---------------------------------------------------------------------|
| Bootstrap Vue for Frontend                      | https://github.com/bootstrap-vue/bootstrap-vue                      |
| CoreUI Free Vue Admin Template v3.1.4 for Admin | https://github.com/coreui/coreui-free-vue-admin-template/tree/3.1.4 |

# Features

* Booking without registration
* Managing Cities
* Managing City Locations
* Managing Test Centers
* Managing Admins
* Managing Bookings