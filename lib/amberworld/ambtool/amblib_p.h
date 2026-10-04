/**
 * @file amblib_p.h
 * AMB archive library.
 * Written by Oliver Gantert.
 */
#ifndef __amblib_p_h__
#define __amblib_p_h__

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include "amblib.h"

/*
#define AMB_DLLEXPORT	__declspec(dllexport)
*/
#define AMB_DLLEXPORT

/* Archive fields are big endian on every host, including AArch64. */
#define AMB_PeekW(mem)		((ushort)(((ushort)((uchar *)(mem))[0] << 8) | ((uchar *)(mem))[1]))
#define AMB_PeekL(mem)		(((amb_u32)((uchar *)(mem))[0] << 24) | ((amb_u32)((uchar *)(mem))[1] << 16) | ((amb_u32)((uchar *)(mem))[2] << 8) | ((uchar *)(mem))[3])
#define AMB_PokeW(mem,w)	{ \
	((uchar *)(mem))[0] = ((ushort)(w) & 0xff00) >> 8; \
	((uchar *)(mem))[1] = ((ushort)(w) & 0x00ff); }
#define AMB_PokeL(mem,l)	{ \
	((uchar *)(mem))[0] = ((l) & 0xff000000) >> 24; \
	((uchar *)(mem))[1] = ((l) & 0x00ff0000) >> 16; \
	((uchar *)(mem))[2] = ((l) & 0x0000ff00) >> 8; \
	((uchar *)(mem))[3] = ((l) & 0x000000ff); }

struct AMB_Archive
{
	FILE *		hFile;
	amb_u32		ulType;
	ushort		usNumFiles;
	ushort		usNextFile;

	amb_u32		ulOffset;
	amb_u32		ulRawSize;
};

#endif /* __amblib_p_h__ */
