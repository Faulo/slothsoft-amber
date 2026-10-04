/**
 * @file amblib.c
 * AMB archive library.
 * 2004-03-20 by Oliver Gantert.
 */
#include "amblib_p.h"

/*
 * (DLL) Version
 */
amb_u32 AMB_DLLEXPORT AMB_LibVersion(void)
{
	return(AMB_LIBVERSION);
}

/*
 * Error Interface
 */

static amb_u32 amb_error = 0;
#define AMB_SetError(e)	{ amb_error = (e); }

amb_u32 AMB_DLLEXPORT AMB_GetError(void)
{
	amb_u32 last_error = amb_error;
	amb_error = 0;
	return(last_error);
}

/*
 * Open/Close Archive
 */

AMB_Archive* AMB_DLLEXPORT AMB_OpenArchive(const char *pszFilename)
{
	AMB_Archive *pArchive;
	amb_u32 ul, size;
	ushort us;

	pArchive = (AMB_Archive *)malloc(sizeof(AMB_Archive));
	if (!pArchive)
	{
		AMB_SetError(AMB_ERR_ALLOC);
		goto label_return;
	}

	pArchive->hFile = fopen(pszFilename, "rb");
	if (!pArchive->hFile)
	{
		AMB_SetError(AMB_ERR_OPEN);
		goto label_free;
	}

	if (fseek(pArchive->hFile, 0, SEEK_END))
	{
		AMB_SetError(AMB_ERR_SEEK);
		goto label_close;
	}
	size = ftell(pArchive->hFile);
	if (fseek(pArchive->hFile, 0, SEEK_SET))
	{
		AMB_SetError(AMB_ERR_SEEK);
		goto label_close;
	}

	if (!fread(&ul, sizeof(amb_u32), 1, pArchive->hFile))
	{
		AMB_SetError(AMB_ERR_READ);
		goto label_close;
	}

	pArchive->ulType = AMB_PeekL(&ul);
	// JH needs special care
	if ((pArchive->ulType & 0xffff0000) == AMB_ID_JH) pArchive->ulType = AMB_ID_JH;

	switch (pArchive->ulType)
	{
		case AMB_ID_JH:
		case AMB_ID_LOB:
		case AMB_ID_VOL1:
			// single-file
			pArchive->usNumFiles = 1;
			pArchive->usNextFile = 0;
			pArchive->ulOffset = 0;
			pArchive->ulRawSize = size;
		break;
		case AMB_ID_AMNC:
		case AMB_ID_AMNP:
		case AMB_ID_AMBR:
		case AMB_ID_AMPC:
			// multi-file archive
			if (!fread(&us, sizeof(ushort), 1, pArchive->hFile))
			{
				AMB_SetError(AMB_ERR_READ);
				goto label_close;
			}
			pArchive->usNumFiles = AMB_PeekW(&us);
			pArchive->usNextFile = 0;
			pArchive->ulOffset = 0;
			pArchive->ulRawSize = size;
		break;
		default:
			AMB_SetError(AMB_ERR_TYPE);
			goto label_close;
		break;
	}

	return(pArchive);

label_close:
	fclose(pArchive->hFile);
label_free:
	free(pArchive);
label_return:
	return(NULL);
}

void AMB_DLLEXPORT AMB_CloseArchive(AMB_Archive *pArchive)
{
	if (pArchive)
	{
		if (pArchive->hFile) fclose(pArchive->hFile);
		free(pArchive);
	}
}

/*
 * Info
 */

amb_u32 AMB_DLLEXPORT AMB_GetArchiveType(AMB_Archive *pArchive)
{
	return(pArchive->ulType);
}

ushort AMB_DLLEXPORT AMB_GetNumFiles(AMB_Archive *pArchive)
{
	return(pArchive->usNumFiles);
}

ushort AMB_DLLEXPORT AMB_GetFileIndex(AMB_Archive *pArchive)
{
	return(pArchive->usNextFile - 1);
}

/*
 * Browse Archive
 */

int AMB_DLLEXPORT AMB_GetFirstFile(AMB_Archive *pArchive)
{
	amb_u32 ul;

	switch (pArchive->ulType)
	{
		case AMB_ID_JH:
		case AMB_ID_LOB:
		case AMB_ID_VOL1:
			// single-file
			pArchive->usNextFile = 1;
			return(pArchive->usNextFile);
		break;
		case AMB_ID_AMNC:
		case AMB_ID_AMNP:
		case AMB_ID_AMBR:
		case AMB_ID_AMPC:
			// multi-file archive
			pArchive->ulOffset = 6 + (pArchive->usNumFiles << 2);
			if (fseek(pArchive->hFile, 6, SEEK_SET))
			{
				AMB_SetError(AMB_ERR_SEEK);
				goto label_return;
			}
			if (!fread(&ul, sizeof(amb_u32), 1, pArchive->hFile))
			{
				AMB_SetError(AMB_ERR_READ);
				goto label_return;
			}
			pArchive->ulRawSize = AMB_PeekL(&ul);
			pArchive->usNextFile = 1;
			return(pArchive->usNextFile);
		break;
		default:
			AMB_SetError(AMB_ERR_TYPE);
			goto label_return;
		break;
	}
label_return:
	return(0);
}

int AMB_DLLEXPORT AMB_GetNextFile(AMB_Archive *pArchive)
{
	amb_u32 ul;

	if (pArchive->usNextFile >= pArchive->usNumFiles)
	{	// no more files
		AMB_SetError(0);
		goto label_return;
	}

	switch (pArchive->ulType)
	{
		case AMB_ID_JH:
		case AMB_ID_LOB:
		case AMB_ID_VOL1:
			// single-file
			pArchive->usNextFile = 1;
			return(pArchive->usNextFile);
		break;
		case AMB_ID_AMNC:
		case AMB_ID_AMNP:
		case AMB_ID_AMBR:
		case AMB_ID_AMPC:
			// multi-file archive
			if (!pArchive->usNextFile)
			{	// get first file
				return(AMB_GetFirstFile(pArchive));
			}
			pArchive->ulOffset += pArchive->ulRawSize;
			if (fseek(pArchive->hFile, 6 + (pArchive->usNextFile << 2), SEEK_SET))
			{
				AMB_SetError(AMB_ERR_SEEK);
				goto label_return;
			}
			if (!fread(&ul, sizeof(amb_u32), 1, pArchive->hFile))
			{
				AMB_SetError(AMB_ERR_READ);
				goto label_return;
			}
			pArchive->ulRawSize = AMB_PeekL(&ul);
			pArchive->usNextFile++;
			return(pArchive->usNextFile);
		break;
		default:
			AMB_SetError(AMB_ERR_TYPE);
			goto label_return;
		break;
	}
label_return:
	return(0);
}

amb_u32 AMB_DLLEXPORT AMB_GetRawFileSize(AMB_Archive *pArchive)
{
	return(pArchive->ulRawSize);
}

amb_u32 AMB_DLLEXPORT AMB_ReadRawFile(AMB_Archive *pArchive, void *pBuffer)
{
	return(AMB_ReadRawFileEx(pArchive, pBuffer, 0, pArchive->ulRawSize));
}

amb_u32 AMB_DLLEXPORT AMB_ReadRawFileEx(AMB_Archive *pArchive, void *pBuffer, amb_u32 ulOffset, amb_u32 ulSize)
{
	if (!pArchive->ulRawSize || !ulSize)
	{
		AMB_SetError(0);
		goto label_return;
	}

	if ((ulOffset + ulSize) > pArchive->ulRawSize)
	{
		AMB_SetError(AMB_ERR_PARAM);
		goto label_return;
	}

	if (fseek(pArchive->hFile, pArchive->ulOffset + ulOffset, SEEK_SET))
	{
		AMB_SetError(AMB_ERR_SEEK);
		goto label_return;
	}

	if (ulSize != fread(pBuffer, 1, ulSize, pArchive->hFile))
	{
		AMB_SetError(AMB_ERR_READ);
		goto label_return;
	}

	return(ulSize);

label_return:
	return(0);
}

amb_u32 AMB_DLLEXPORT AMB_GetFileSize(AMB_Archive *pArchive)
{
	return(AMB_ReadFile(pArchive, NULL));
}

amb_u32 AMB_DLLEXPORT AMB_ReadFile(AMB_Archive *pArchive, void *pBuffer)
{
	void *pTemp;
	uchar *pPosition;
	amb_u32 ulSize, ulRounded, ulType;

	ulSize = pArchive->ulRawSize;
	if (!ulSize)
	{
		AMB_SetError(0);
		goto label_return;
	}

	ulRounded = ulSize & 1 ? ulSize + 1 : ulSize;
	pTemp = malloc(ulRounded);
	if (!pTemp)
	{
		AMB_SetError(AMB_ERR_ALLOC);
		goto label_return;
	}

	if (!AMB_ReadRawFile(pArchive, pTemp))
	{
		goto label_free;
	}

	pPosition = (uchar *)pTemp;

	// decode a JH file
	ulType = AMB_PeekL(pPosition);
	if ((ulType & 0xffff0000) == AMB_ID_JH)
	{
		pPosition += 4;
		ulSize -= 4;
		AMB_JHCodec(pPosition, ulSize, ((ulType & 0xffff0000) >> 16) ^ (ulType & 0x0000ffff));
	}
	else
	{
		// AMNC archives are always encoded
		if (pArchive->ulType == AMB_ID_AMNC)
		{
			AMB_JHCodec(pPosition, ulSize, pArchive->usNextFile);
		}
	}

	// see if it's a LOB file
	ulType = AMB_PeekL(pPosition);
	if (ulType == AMB_ID_LOB || ulType == AMB_ID_VOL1)
	{
		pPosition += 4;
		// AMNP archives are always encoded
		if (pArchive->ulType == AMB_ID_AMNP)
		{
			AMB_JHCodec(pPosition + 4, ulSize - 8, pArchive->usNextFile);
		}
		ulSize = AMB_PeekL(pPosition) & 0x00ffffff;
		if (pBuffer) AMB_UnLOB(pPosition + 8, pBuffer, ulSize);
	}
	else
	{
		// AMNP archives are always encoded
		if (pArchive->ulType == AMB_ID_AMNP)
		{
			AMB_JHCodec(pPosition + 4, ulSize - 4, pArchive->usNextFile);
		}
		if (pBuffer) memcpy(pBuffer, pPosition, ulSize);
	}

	free(pTemp);
	return(ulSize);

label_free:
	free(pTemp);
label_return:
	return(0);
}

/*
 * JH En/Decoder
 */

void AMB_DLLEXPORT AMB_JHCodec(void *pBuffer, amb_u32 ulSize, ushort usKey)
{
	ushort *a0 = (ushort *)pBuffer;
	ushort d0 = usKey, d1;
	amb_u32 d7 = (ulSize + 1) >> 1;

	while (d7--)
	{
		AMB_PokeW(a0, AMB_PeekW(a0) ^ d0);
		++a0;
		d1 = d0;
		d0 <<= 4;
		d0 += (d1 + 87);
	}
}

/*
 * LOB Decompressor
 */

void AMB_DLLEXPORT AMB_UnLOB(void *src, void *dst, amb_u32 dstsize)
{
	uchar	*a0 = (uchar *)src,
			*a1 = (uchar *)dst,
			*a2;
	amb_u32	d0 = dstsize;
	ushort	d1 = 0x80,
			d3;
	uchar	d2,
			xflag,
			carry;

	while (d0)
	{
		d1 += d1;
		xflag = (d1 > 0xff);
		carry = xflag;
		d1 &= 0x00ff;
		if (!d1)
		{
			d1 = *(a0++);
			d1 += d1;
			if (xflag) ++d1;
			carry = (d1 > 0xff);
			d1 &= 0x00ff;
		}

		if (!carry)	/* fixed 2003-08-25 */
		{
			d3 = *(a0++);
			d2 = (d3 & 0x000f) + 3;
			d3 <<= 4;
			d3 &= 0xff00;
			d3 |= *(a0++);
			a2 = a1 - d3;
			while (d2--)
			{
				*(a1++) = *(a2++);
				--d0;
			}
		}
		else
		{
			*(a1++) = *(a0++);
			--d0;
		}
	}
}
