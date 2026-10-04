#!/bin/sh
set -eu

target=${1:?Expected win-x64, linux-x64, or linux-arm64}
case "$target" in
    win-x64)
        cc=${CC:-x86_64-w64-mingw32-gcc}
        extension=exe
        link_flags='-static -Wl,--no-insert-timestamp'
        ;;
    linux-x64)
        cc=${CC:-gcc}
        extension=x64
        link_flags=-static
        ;;
    linux-arm64)
        cc=${CC:-aarch64-linux-gnu-gcc}
        extension=arm64
        link_flags=-static
        ;;
    *)
        echo "Unsupported target: $target" >&2
        exit 1
        ;;
esac

source_dir=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
output_dir="$source_dir/../../assets/cli"
mkdir -p "$output_dir/ambtool" "$output_dir/amgfx"

"$cc" -std=gnu99 -O2 -Wall -Wextra $link_flags \
    "$source_dir/ambtool/ambtool.c" "$source_dir/ambtool/am_os.c" "$source_dir/ambtool/amblib.c" \
    -o "$output_dir/ambtool/ambtool.$extension"
"$cc" -std=gnu99 -O2 -Wall -Wextra $link_flags \
    "$source_dir/amgfx/amgfx.c" "$source_dir/amgfx/SaveTGA8.c" \
    -o "$output_dir/amgfx/amgfx.$extension"
