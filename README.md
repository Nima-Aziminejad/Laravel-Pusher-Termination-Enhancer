# Laravel Broadcasting User Termination Package

The Laravel Broadcasting User Termination Package is a Laravel package that enhances Laravel's broadcasting feature by adding a user termination attribute. This package integrates seamlessly with the Pusher third-party service, allowing you to manage user terminations effectively.

## Introduction

Laravel Broadcasting User Termination Package extends Laravel Broadcasting with a user termination attribute. This feature allows you to mark users as terminated, preventing them from receiving broadcasts. It's particularly useful when integrating with real-time applications and ensuring that inactive or banned users no longer receive updates.

## Features

- User Termination Attribute: Add a `terminated_at` attribute to user models to indicate termination.
- Automatic Filtering: Automatically filter out terminated users from broadcast recipients.
- Pusher Integration: Built to work seamlessly with the Pusher third-party service.
