# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres
to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

- Add wpautop filter

### Added

- Current user in global
- bbPress compatibility: register all `bbp_get_{$type}_template` filters to enable Twig template resolution for all bbPress template types

### Fixed

- Fixed fatal error when an user_id from a deleted user is used as post author.