<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:sse="http://schema.slothsoft.net/savegame/editor" xmlns:sfx="http://schema.slothsoft.net/farah/xslt"
    xmlns:exsl="http://exslt.org/common" xmlns:func="http://exslt.org/functions" extension-element-prefixes="func" xmlns:sfd="http://schema.slothsoft.net/farah/dictionary">

    <xsl:variable name="portraits" select="document('farah://slothsoft@amber/api/amberdata?infosetId=lib.portraits')/saa:amberdata/saa:portrait-list" />

    <xsl:template match="*" mode="portrait-picker">
        <xsl:variable name="value" select="@value" />

        <amber-embed infoset="lib.portraits" type="portrait" id="{@value}" mode="popup picker">
            <xsl:apply-templates select="." mode="form-picker" />
        </amber-embed>

        <xsl:for-each select="$portraits">
            <select class="amber-editor__input amber-editor__input--select amber-editor__input--widget amber-text" data-editor-action="apply-portrait">
                <xsl:for-each select="*">
                    <optgroup label="{@name}">
                        <xsl:for-each select="*">
                            <option value="{@id}">
                                <xsl:if test="@id = $value">
                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                </xsl:if>
                                <xsl:value-of select="concat('Portrait #', @id)" />
                            </option>
                        </xsl:for-each>
                    </optgroup>
                </xsl:for-each>
            </select>
        </xsl:for-each>
    </xsl:template>
</xsl:stylesheet>