# Amberworld port changelog

## Unreleased

Port of AMBtool 0.6 with AMBlib 0.5 and AMgfx 0.4 for Linux x64, Linux ARM64,
and Windows x64. Changes below are relative to the source archives linked in
[README.md](README.md).

### AMBlib and AMBtool

- `ambtool/amblib.h`, `ambtool/amblib_p.h`, `ambtool/amblib.c`, and
  `ambtool/ambtool.c`: use `<stdint.h>` integer types for archive fields,
  file sizes, and related APIs. In particular, the former `ulong` is now
  `amb_u32` (`uint32_t`), so reading an archive field always consumes four
  bytes on 64-bit platforms and avoids the POSIX `ulong` name collision.
- `ambtool/amblib_p.h`: decode and encode archive words and longwords as
  explicit big-endian bytes instead of selecting macros by host endianness.
  This also handles little-endian AArch64 hosts.
- `ambtool/am_os.h` and `ambtool/am_os.c`: recognize `_WIN32` as well as
  `WIN32`; implement directory creation on POSIX with `mkdir(path, 0777)`,
  accepting an existing directory. The Windows and Amiga branches remain.
- `ambtool/ambtool.c`: bound generated extraction paths with `snprintf` and
  print the fixed-width sizes with `%u`. The console format descriptions used
  by the PHP integration remain the same.

### AMgfx

- `amgfx/amgfx.h` and `amgfx/amgfx.c`: use fixed-width integer types for bit
  offsets and map `strnicmp` to POSIX `strncasecmp` outside Windows and Amiga.
- `amgfx/SaveTGA8.c` and `amgfx/palettes.h` retain the upstream source.

### Builds and distribution

- `build.sh` compiles both tools for Linux x64, Linux ARM64, and Windows x64.
  Linux and Windows builds are statically linked; the Windows linker timestamp
  is disabled for reproducible output.
- The six resulting executables are stored under `assets/cli/ambtool/` and
  `assets/cli/amgfx/`. The original 32-bit Windows executables were removed.
  `ambermap` and its runtime files remain separate under `assets/cli/ambermap/`.
