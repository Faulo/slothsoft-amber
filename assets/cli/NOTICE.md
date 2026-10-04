# Amberworld command-line tools

The bundled AMBtool and AMgfx executables are rebuilt from the Amberworld project's AMBtool 0.6,
AMBlib 0.5, and AMgfx 0.4 sources by Oliver Gantert. Credit belongs to the
Amberworld project: <https://amberworld.sourceforge.net/>.

The original source archives and their readmes are available at:

- <https://sourceforge.net/projects/amberworld/files/ambtool/0.6/ambtool-0.6-src.zip/download>
- <https://sourceforge.net/projects/amberworld/files/amgfx/0.4/amgfx-0.4-src.zip/download>

The original readmes request credit and a link to Amberworld when using source
code. They do not identify a standard software license. The MIT license in this
package's root `LICENSE` does not apply to these third-party tools.

The ported sources and build script are stored in `lib/amberworld` in
the Git repository. Composer distribution archives contain the three builds
of each tool and this notice, while `.gitattributes` excludes the source
directory. The original 32-bit Ambermap executable, its DLLs, and game data
remain together in `assets/cli/ambermap`.
