#include <stdio.h>
#include <stdint.h>
#include <string.h>
#include "amblib_p.h"

#define CHECK(condition) do { \
    if (!(condition)) { \
        fprintf(stderr, "%s failed at line %d\n", #condition, __LINE__); \
        return 1; \
    } \
} while (0)

int main(int argc, char **argv)
{
    uchar bytes[4] = { 0x12, 0x34, 0x56, 0x78 };
    AMB_Archive *archive;

    CHECK(argc == 2);
    CHECK(sizeof(uchar) == 1);
    CHECK(sizeof(ushort) == 2);
    CHECK(sizeof(amb_u32) == 4);
    CHECK(AMB_PeekW(bytes) == 0x1234);
    CHECK(AMB_PeekL(bytes) == UINT32_C(0x12345678));

    AMB_PokeW(bytes, 0xabcd);
    CHECK(bytes[0] == 0xab && bytes[1] == 0xcd);
    AMB_PokeL(bytes, UINT32_C(0x89abcdef));
    CHECK(memcmp(bytes, "\x89\xab\xcd\xef", sizeof(bytes)) == 0);

    archive = AMB_OpenArchive(argv[1]);
    CHECK(archive != NULL);
    CHECK(AMB_GetArchiveType(archive) == AMB_ID_AMNP);
    CHECK(AMB_GetNumFiles(archive) == 7);
    CHECK(AMB_GetNextFile(archive) == 1);
    CHECK(AMB_GetNextFile(archive) == 2);
    CHECK(AMB_GetNextFile(archive) == 3);
    CHECK(AMB_GetRawFileSize(archive) > 0);
    AMB_CloseArchive(archive);
    return 0;
}
