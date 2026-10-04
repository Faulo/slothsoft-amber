/*
 _______          __ http://amberworld.sourceforge.net __    __
|   _   |--------.  |--.-----.----.--.--.--.-----.----.  |--|  |
|       |        |  _  |  -__|   _|  |  |  |  _  |   _|  |  _  |
|___|___|__|__|__|_____|_____|__| |________|_____|__| |__|_____|
         AMBERWORLD - THE AMBERMOON OPEN SOURCE PROJECT
*/
#ifndef __amgfx_h__
#define __amgfx_h__

#include <stdint.h>

typedef uint8_t	uchar;
typedef uint16_t	ushort;
typedef uint32_t	amb_u32;

extern int SaveTGA8(const char *fname,
	uchar *pixels, ushort width, ushort height,
	uchar *palette, ushort numColors);

#endif /* __amgfx_h__ */
