/**
 * @file amblib.h
 * AMB archive library.
 * Written by Oliver Gantert.
 */
#ifndef __amblib_h__
#define __amblib_h__

/*
 * Types
 */

#include <stdint.h>

typedef uint8_t	uchar;
typedef uint16_t	ushort;
typedef uint32_t	amb_u32;

typedef struct AMB_Archive	AMB_Archive;

/*
 * Version
 */

#define AMB_VERSION_MAJOR	0
#define AMB_VERSION_MINOR	5

#define AMB_LIBVERSION	((AMB_VERSION_MAJOR << 8) | AMB_VERSION_MINOR)
extern amb_u32 AMB_LibVersion(void);

/*
 * Error
 */

#define AMB_ERR_ALLOC	1
#define AMB_ERR_OPEN	2
#define AMB_ERR_READ	3
#define AMB_ERR_TYPE	4
#define AMB_ERR_SEEK	5
#define AMB_ERR_PARAM	6

extern amb_u32 AMB_GetError(void);

/*
 * ArchiveType IDs
 */

#define AMB_ID_JH		(0x4a480000)
#define AMB_ID_LOB		(0x014c4f42)
#define AMB_ID_VOL1		(0x564f4c31)
#define AMB_ID_AMNC		(0x414d4e43)
#define AMB_ID_AMNP		(0x414d4e50)
#define AMB_ID_AMBR		(0x414d4252)
#define AMB_ID_AMPC		(0x414d5043)

/*
 * OpenArchive returns a handle to a private archive structure.
 * This must be freed by a call to CloseArchive when finished.
 */
extern AMB_Archive *AMB_OpenArchive(const char *pszFilename);
extern void AMB_CloseArchive(AMB_Archive *pArchive);

/*
 * GetArchiveType returns one of the AMB_ID_xxx values.
 */
extern amb_u32 AMB_GetArchiveType(AMB_Archive *pArchive);

/*
 * GetNumFiles returns the number of entries in the archive.
 */
extern ushort AMB_GetNumFiles(AMB_Archive *pArchive);

/*
 * GetFirstFile sets the current file to the first entry.
 */
extern int AMB_GetFirstFile(AMB_Archive *pArchive);

/*
 * GetNextFile sets the current file to the next entry
 * or returns 0 on error. If there are no more files in
 * the archive, GetNextFile returns 0 and sets the error
 * code to 0. Use GetError to check this.
 */
extern int AMB_GetNextFile(AMB_Archive *pArchive);

/*
 * GetRawFileSize returns the raw size of the current file entry.
 * Please note that some entries could have a size of 0 bytes!
 */
extern amb_u32 AMB_GetRawFileSize(AMB_Archive *pArchive);

/*
 * ReadRawFile loads the raw data of the current file entry into pBuffer.
 * This buffer must be allocated by the user. Use GetRawFileSize to
 * determine the required size. ReadRawFile returns the number of
 * bytes read or 0 on error.
 */
extern amb_u32 AMB_ReadRawFile(AMB_Archive *pArchive, void *pBuffer);
extern amb_u32 AMB_ReadRawFileEx(AMB_Archive *pArchive, void *pBuffer, amb_u32 ulOffset, amb_u32 ulSize);

/*
 * GetFileSize returns the uncompressed size of the current file entry.
 * Please note that some entries could have a size of 0 bytes!
 */
extern amb_u32 AMB_GetFileSize(AMB_Archive *pArchive);

/*
 * ReadFile loads the uncompressed data of the current file entry into pBuffer.
 * This buffer must be allocated by the user. Use GetFileSize to
 * determine the required size. ReadFile returns the number of
 * bytes read or 0 on error.
 */
extern amb_u32 AMB_ReadFile(AMB_Archive *pArchive, void *pBuffer);

/*
 * These are used by GetFileSize and ReadFile to decrypt and
 * uncompress files from an archive.
 */
extern void AMB_JHCodec(void *pBuffer, amb_u32 ulSize, ushort usKey);
extern void AMB_UnLOB(void *src, void *dst, amb_u32 dstsize);

extern ushort AMB_GetFileIndex(AMB_Archive *pArchive);

#endif /* __amblib_h__ */
