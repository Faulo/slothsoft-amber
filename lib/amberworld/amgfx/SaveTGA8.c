/*
 _______          __ http://amberworld.sourceforge.net __    __
|   _   |--------.  |--.-----.----.--.--.--.-----.----.  |--|  |
|       |        |  _  |  -__|   _|  |  |  |  _  |   _|  |  _  |
|___|___|__|__|__|_____|_____|__| |________|_____|__| |__|_____|
         AMBERWORLD - THE AMBERMOON OPEN SOURCE PROJECT
*/
#include <stdio.h>
#include "amgfx.h"

typedef struct
{
	uchar	idLen;
	uchar	cmapType;
	uchar	imgType;
	uchar	cmapOrigin[2];
	uchar	cmapLength[2];
	uchar	cmapEntrySize;
	uchar	originX[2];
	uchar	originY[2];
	uchar	width[2];
	uchar	height[2];
	uchar	pixelSize;
	uchar	imgDesc;
} TGAheader;

/* poke little endian word */
#define pokew(p,w)	{	\
	((uchar *)(p))[0] = (uchar)(((ushort)(w))&0x00ff);	\
	((uchar *)(p))[1] = (uchar)((((ushort)(w))&0xff00)>>8);	\
	}

int SaveTGA8(const char *fname,
	uchar *pixels, ushort width, ushort height,
	uchar *palette, ushort numColors)
{
	FILE *file;
	TGAheader head;
	uchar *cmap, col[3];
	ushort i;

	file = fopen(fname, "wb");
	if (!file) return(0);

	/* save header */
	head.idLen = 0;
	head.cmapType = head.imgType = 1;
	head.cmapOrigin[0] = head.cmapOrigin[1] = 0;
	pokew(head.cmapLength, numColors);
	head.cmapEntrySize = 24;	/* bits per color */
	head.originX[0] = head.originX[1] = 0;
	head.originY[0] = head.originY[1] = 0;
	pokew(head.width, width);
	pokew(head.height, height);
	head.pixelSize = 8;	/* bits per pixel */
	head.imgDesc = 32;	/* set bit 5 (origin left upper corner) */
	if (!fwrite(&head, sizeof(TGAheader), 1, file)) goto label_close;

	/* save colormap */
	cmap = palette;
	for (i = numColors; i--;)
	{
		col[0] = cmap[2];	/* B */
		col[1] = cmap[1];	/* G */
		col[2] = cmap[0];	/* R */
		if (!fwrite(col, 3, 1, file)) goto label_close;
		cmap += 3;
	}

	/* save pixels */
	if (fwrite(pixels, width, height, file) == height)
	{
		fclose(file);
		return(1);
	}

label_close:
	fclose(file);
	return(0);
}
