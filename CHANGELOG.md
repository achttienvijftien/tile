# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres
to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

- Add wpautop filter

### Added

- Current user in global
- bbPress compatibility: register all `bbp_get_{$type}_template` filters to enable Twig template resolution for all bbPress template types
- Compiled Twig templates are cached on disk, in the service container's `%kernel.cache_dir%/tile-twig`. Without the container nothing is cached unless `TILE_TWIG_CACHE` names a directory. Templates are checked for changes while developing (`kernel.debug`, so `local` and `development`); on other container sites they are compiled once per cache directory. When the directory is not writable, templates are compiled in memory as before.
- `TILE_TWIG_CACHE` constant: a directory for the compiled templates, or `false` to switch the cache off
- `TILE_TWIG_AUTO_RELOAD` constant: `true` keeps checking templates for changes outside development, for container sites that deploy into the same directory; `false` stops it on sites without the container. Ignored while developing, where it is always on
- `tile_twig_environment_options` filter and `tile_twig_environment` action, to configure the Twig environment; register them before the first render

### Fixed

- Fixed fatal error when an user_id from a deleted user is used as post author.