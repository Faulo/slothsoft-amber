# Amberworld native tools

This directory contains a port of AMBtool 0.6 (using AMBlib 0.5) and AMgfx
0.4 by Oliver Gantert. The source archives were downloaded from:

- <https://sourceforge.net/projects/amberworld/files/ambtool/0.6/ambtool-0.6-src.zip/download> — SHA-256 `7ab674135f5c327dd0228224ab8d1c05ed7b0d7d1512bc6280e18522da5d26d8`
- <https://sourceforge.net/projects/amberworld/files/amgfx/0.4/amgfx-0.4-src.zip/download> — SHA-256 `dc9fda131ffedf4d77667e096b6528d2b22c4896ebac5faec34721c6cbf8eac7`

The original `readme.txt` and `Amberworld.txt` files are retained beside each
tool. The readmes request credit and a link to Amberworld when using source.
They do not identify a standard software license; see the distribution notice
at `assets/cli/NOTICE.md` before redistributing the rebuilt executables.

The changes made for this port are documented in [CHANGELOG.md](CHANGELOG.md).

## Build

Run `sh lib/amberworld/build.sh TARGET` from the repository root,
where `TARGET` is `linux-x64`, `linux-arm64`, or `win-x64`. The script writes
the binaries as `assets/cli/ambtool/ambtool.EXT` and
`assets/cli/amgfx/amgfx.EXT`, where `EXT` is `exe`, `x64`, or `arm64`.

The checked-in binaries were built in the DDEV Debian 13 web container with
GCC 14.2.0 for Linux x64, GCC 14.2.0 for Linux ARM64, and MinGW-w64 GCC
14-win32 for Windows x64. Linux binaries are statically linked. The Windows
binaries were cross-compiled in DDEV and run against the fixtures on Windows.
The Windows linker timestamp is disabled for reproducible output.

To build and test the source without writing to `assets`, run
`cmake -S lib/amberworld -B build/lib-amberworld`, then
`cmake --build build/lib-amberworld` and
`ctest --test-dir build/lib-amberworld --output-on-failure`.
CTest runs AMBlib format checks and compares output from freshly built
AMBtool and AMgfx executables with the canonical `test-files` fixtures.
The `lib-tests.yml` workflow runs these tests on native Windows x64, Linux x64,
and Linux ARM64 runners.
