<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
	xmlns:sse="http://schema.slothsoft.net/savegame/editor">	
	
	<xsl:import href="globals/dictionary" />
	<xsl:import href="globals/savegame" />
	<xsl:import href="globals/editor" />
	<xsl:import href="globals/picker" />
	<xsl:import href="globals/maps" />
	

	<xsl:template match="sse:archive[@name='1Map_data.amb']" mode="form-content">
		<xsl:call-template name="maps.data-file"/>
	</xsl:template>
	<xsl:template match="sse:archive[@name='1Map_texts.amb']" mode="form-content">
		<xsl:call-template name="maps.text-file"/>
	</xsl:template>
	
	<xsl:template match="sse:archive[@name='2Map_data.amb']" mode="form-content">
		<xsl:call-template name="maps.data-file"/>
	</xsl:template>
	<xsl:template match="sse:archive[@name='2Lab_data.amb']"		mode="form-content">
		<xsl:call-template name="maps.tileset-file"/>
	</xsl:template>
	<xsl:template match="sse:archive[@name='2Map_texts.amb']"
		mode="form-content">
		<xsl:call-template name="maps.text-file"/>
	</xsl:template>
	
	<xsl:template match="sse:archive[@name='3Map_data.amb']" mode="form-content">
		<xsl:call-template name="maps.data-file"/>
	</xsl:template>
	<xsl:template match="sse:archive[@name='3Lab_data.amb']"		mode="form-content">
		<xsl:call-template name="maps.tileset-file"/>
	</xsl:template>
	<xsl:template match="sse:archive[@name='3Map_texts.amb']"
		mode="form-content">
		<xsl:call-template name="maps.text-file"/>
	</xsl:template>
	
	<xsl:template match="sse:archive[@name='Icon_data.amb']"		mode="form-content">
		<xsl:call-template name="maps.tileset-file"/>
	</xsl:template>
</xsl:stylesheet>