# Changelog

All notable changes to `laravel-short-schedule` will be documented in this file

## 1.6.1 - 2024-05-16

- do not fail if shortScheduel is not defined

**Full Changelog**: https://github.com/spatie/laravel-short-schedule/compare/1.6.0...1.6.1

## 1.6.0 - 2024-03-27

- add `ShortSchedule` facade

**Full Changelog**: https://github.com/spatie/laravel-short-schedule/compare/1.5.2...1.6.0

## 1.5.2 - 2024-03-07

### What's Changed

* Fix typo in readme by @benholmen in https://github.com/spatie/laravel-short-schedule/pull/91
* Laravel 11.x Compatibility by @laravel-shift in https://github.com/spatie/laravel-short-schedule/pull/90

### New Contributors

* @benholmen made their first contribution in https://github.com/spatie/laravel-short-schedule/pull/91
* @laravel-shift made their first contribution in https://github.com/spatie/laravel-short-schedule/pull/90

**Full Changelog**: https://github.com/spatie/laravel-short-schedule/compare/1.5.1...1.5.2

## 1.5.1 - 2023-01-23

- support L10

## 1.5.0 - 2022-01-14

- Support Laravel 9

## 1.4.5 - 2022-01-10

## What's Changed

- PendingShortScheduleCommand::command method will attempt to resolve command name if class name was given by @etahamer in https://github.com/spatie/laravel-short-schedule/pull/54

## New Contributors

- @etahamer made their first contribution in https://github.com/spatie/laravel-short-schedule/pull/54

**Full Changelog**: https://github.com/spatie/laravel-short-schedule/compare/1.4.4...1.4.5

## 1.4.4 - 2021-12-09

## What's Changed

- Using the PHP Version the scheduler is called with for artisan commands by @TimGeDev in https://github.com/spatie/laravel-short-schedule/pull/50

## New Contributors

- @TimGeDev made their first contribution in https://github.com/spatie/laravel-short-schedule/pull/50

**Full Changelog**: https://github.com/spatie/laravel-short-schedule/compare/1.4.3...1.4.4

## 1.4.3 - 2021-07-22

- fixed description and help message (#37)

## 1.4.2 - 2021-06-11

- allow spatie/temporary-directory 2.* (#35)

## 1.4.1 - 2021-06-04

- do not set a default lifetime for production

## 1.4.0 - 2021-05-31

- add lifetime option

## 1.3.0 - 2020-12-24

- add PHP8 support (#25)

## 1.2.2 - 2020-09-08

- add support for Laravel 8

## 1.2.1 - 2020-07-14

- fix for tasks not getting executed if the command is started by supervisord.

## 1.2.0 - 2020-07-13

- added `onOneServer` option to short run commands (#8)

## 1.1.0 - 2020-06-17

- adding support for Maintenance Mode (#4)

## 1.0.0 - 2020-06-07

- initial release
