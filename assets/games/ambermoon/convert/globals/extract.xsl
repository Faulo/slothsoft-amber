<xsl:stylesheet version="1.0" xmlns="http://schema.slothsoft.net/amber/amberdata" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:sse="http://schema.slothsoft.net/savegame/editor" xmlns:str="http://exslt.org/strings">

    <xsl:template name="extract-languages">
        <xsl:param name="root" select="." />
        <xsl:for-each select=".//*[@name = 'languages']">
            <xsl:for-each select=".//*[string-length(@value) &gt; 0]">
                <saa:language name="{saa:getName()}" />
            </xsl:for-each>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="extract-spellbook-instance">
        <xsl:param name="root" select="." />

        <xsl:for-each select="$root//*[@name = 'spellbooks']/*">
            <xsl:variable name="pos" select="position()" />
            <xsl:variable name="spellbook-id" select="$pos - 1" />
            <xsl:variable name="spellbook-dict" select="concat('spells-', $spellbook-id)" />
            <xsl:variable name="spellbook-spells" select="$root//*[@name = 'spells']/*[$pos]" />
            <xsl:if test="@value">
                <saa:spellbook-instance id="{$spellbook-id}" name="{saa:getDictionaryOption(../@dictionary-ref, $spellbook-id)/@val}">
                    <xsl:apply-templates select="$spellbook-spells" mode="reference">
                        <xsl:with-param name="name" select="'spell-reference'" />
                        <xsl:with-param name="dictionary-ref" select="$spellbook-dict" />
                    </xsl:apply-templates>
                </saa:spellbook-instance>
            </xsl:if>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="extract-equipment">
        <xsl:param name="root" select="." />
        <xsl:for-each select="$root//*[@name = 'equipment']">
            <saa:equipment>
                <xsl:for-each select="*">
                    <xsl:call-template name="extract-slot" />
                </xsl:for-each>
            </saa:equipment>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="extract-inventory">
        <xsl:param name="root" select="." />
        <xsl:for-each select="$root//*[@name = 'inventory']">
            <saa:inventory>
                <xsl:for-each select="*">
                    <xsl:call-template name="extract-slot" />
                </xsl:for-each>
            </saa:inventory>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="extract-slot">
        <xsl:param name="root" select="." />
        <saa:slot>
            <xsl:if test="$root/@name">
                <xsl:attribute name="name"><xsl:value-of select="@name" /></xsl:attribute>
            </xsl:if>
            <xsl:for-each select="$root//*[@name = 'item-id'][@value &gt; 0]/..">
                <xsl:call-template name="extract-item-instance" />
            </xsl:for-each>
        </saa:slot>
    </xsl:template>

    <xsl:template name="extract-item-instance">
        <xsl:param name="root" select="." />
        <saa:item-instance>
            <xsl:apply-templates select="$root//*[@name = 'item-id']" mode="attr" />
            <xsl:apply-templates select="$root//*[@name = 'item-amount']" mode="attr" />
            <xsl:apply-templates select="$root//*[@name = 'item-charge']" mode="attr" />

            <xsl:for-each select=".//*[@name = 'item-status']/*[@value != '']">
                <xsl:attribute name="is-{saa:getName()}" />
            </xsl:for-each>
        </saa:item-instance>
    </xsl:template>

    <xsl:template name="extract-character">
        <xsl:param name="root" select="." />
        <xsl:param name="id" />
        <xsl:param name="dialog" select="/.." />

        <xsl:attribute name="id"><xsl:value-of select="$id" /></xsl:attribute>
        <xsl:attribute name="attack"><xsl:value-of select="*[@name = 'attack']/@value + *[@name = 'combat-attack']/@value" /></xsl:attribute>
        <xsl:attribute name="defense"><xsl:value-of select="*[@name = 'defense']/@value + *[@name = 'combat-defense']/@value" /></xsl:attribute>

        <xsl:apply-templates select=".//*[@name = 'name']" mode="attr" />
        <xsl:apply-templates select=".//*[@name = 'portrait-id']" mode="attr" />
        <xsl:apply-templates select=".//*[@name = 'level']" mode="attr" />
        <xsl:apply-templates select=".//*[@name = 'gender']" mode="attr" />
        <xsl:apply-templates select=".//*[@name = 'attacks-per-round']" mode="attr" />
        <xsl:apply-templates select=".//*[@name = 'gold']" mode="attr" />
        <xsl:apply-templates select=".//*[@name = 'food']" mode="attr" />
        <xsl:apply-templates select=".//*[@name = 'magic-attack']" mode="attr" />
        <xsl:apply-templates select=".//*[@name = 'magic-defense']" mode="attr" />
        <xsl:apply-templates select=".//*[@name = 'spelllearn-points']" mode="attr" />
        <xsl:apply-templates select=".//*[@name = 'training-points']" mode="attr" />

        <xsl:for-each select=".//*[@name = 'monster-type']/*[@value]">
            <xsl:attribute name="is-{saa:getName()}" />
        </xsl:for-each>

        <xsl:call-template name="extract-languages" />
        <xsl:call-template name="extract-equipment" />
        <xsl:call-template name="extract-inventory" />

        <xsl:for-each select=".//*[@type='event-dictionary']/*">
            <saa:event>
                <xsl:for-each select=".//*[@dictionary-ref='event-trigger-types']">
                    <saa:trigger id="{@value}" name="{saa:getDictionaryOption(@dictionary-ref, @value)/@val}">
                        <xsl:apply-templates select="following-sibling::*[@name = 'event-payload']" mode="attr">
                            <xsl:with-param name="name" select="'value'" />
                        </xsl:apply-templates>
                    </saa:trigger>
                </xsl:for-each>
                <xsl:for-each select=".//*[@dictionary-ref='event-types'][@value=17]/following-sibling::*[@name = 'event-payload']">
                    <xsl:variable name="id" select="@value" />
                    <xsl:variable name="text" select="($dialog//sse:string)[(position() - 1) = $id]" />
                    <xsl:call-template name="extract-text">
                        <xsl:with-param name="id" select="$id" />
                        <xsl:with-param name="root" select="$text/@value" />
                    </xsl:call-template>
                </xsl:for-each>
            </saa:event>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="extract-class">
        <xsl:param name="id" />
        <xsl:param name="root" />
        <xsl:param name="base-experience" />
        <saa:class>
            <xsl:apply-templates select=".//*[@name = 'class']" mode="attr">
                <xsl:with-param name="name" select="'name'" />
            </xsl:apply-templates>

            <xsl:apply-templates select="$root//*[@name = 'apr-per-level']" mode="attr" />
            <xsl:apply-templates select="$root//*[@name = 'hp-per-level']" mode="attr" />
            <xsl:apply-templates select="$root//*[@name = 'sp-per-level']" mode="attr" />
            <xsl:apply-templates select="$root//*[@name = 'tp-per-level']" mode="attr" />
            <xsl:apply-templates select="$root//*[@name = 'slp-per-level']" mode="attr" />

            <xsl:apply-templates select="$base-experience" mode="attr">
                <xsl:with-param name="name" select="'base-experience'" />
            </xsl:apply-templates>

            <xsl:for-each select="$root//*[@name = 'skills']/*">
                <saa:skill name="{saa:getName()}" maximum="{*[@name = 'maximum']/@value}" />
            </xsl:for-each>

            <xsl:apply-templates select="$root//*[@name='spellbooks']" mode="reference">
                <xsl:with-param name="name" select="'spellbook-reference'" />
            </xsl:apply-templates>
        </saa:class>
    </xsl:template>

    <xsl:template name="extract-class-instance">
        <xsl:param name="root" />
        <saa:class-instance>
            <xsl:apply-templates select=".//*[@name = 'class']" mode="attr">
                <xsl:with-param name="name" select="'name'" />
            </xsl:apply-templates>

            <xsl:apply-templates select="$root//*[@name = 'apr-per-level']" mode="attr" />
            <xsl:apply-templates select="$root//*[@name = 'hp-per-level']" mode="attr" />
            <xsl:apply-templates select="$root//*[@name = 'sp-per-level']" mode="attr" />
            <xsl:apply-templates select="$root//*[@name = 'tp-per-level']" mode="attr" />
            <xsl:apply-templates select="$root//*[@name = 'slp-per-level']" mode="attr" />
            <xsl:apply-templates select=".//*[@name = 'experience']" mode="attr" />

            <xsl:apply-templates select="$root[../@name = 'class-experience']//sse:integer" mode="attr">
                <xsl:with-param name="name" select="'base-experience'" />
            </xsl:apply-templates>

            <xsl:for-each select="$root//*[@name = 'skills']/*">
                <saa:skill name="{saa:getName()}" current="{*[@name = 'current']/@value + *[@name = 'current-mod']/@value}" maximum="{*[@name = 'maximum']/@value}" />
            </xsl:for-each>

            <xsl:variable name="hp" select="*[@name = 'hit-points']/*" />
            <saa:hp current="{$hp[@name='current']/@value}" maximum="{$hp[@name='maximum']/@value + $hp[@name='maximum-mod']/@value}" />

            <xsl:variable name="sp" select="*[@name = 'spell-points']/*" />
            <saa:sp current="{$sp[@name='current']/@value}" maximum="{$sp[@name='maximum']/@value + $sp[@name='maximum-mod']/@value}" />

            <xsl:call-template name="extract-spellbook-instance">
                <xsl:with-param name="root" select="$root" />
            </xsl:call-template>
        </saa:class-instance>
    </xsl:template>

    <xsl:template name="extract-race">
        <xsl:param name="root" />
        <saa:race>
            <xsl:apply-templates select=".//*[@name = 'race']" mode="attr">
                <xsl:with-param name="name" select="'name'" />
            </xsl:apply-templates>
            <xsl:apply-templates select=".//*[@name = 'age']//*[@name = 'current']" mode="attr">
                <xsl:with-param name="name" select="'age-current'" />
            </xsl:apply-templates>
            <xsl:apply-templates select=".//*[@name = 'age']//*[@name = 'maximum']" mode="attr">
                <xsl:with-param name="name" select="'age-maximum'" />
            </xsl:apply-templates>
            <xsl:for-each select=".//*[@name = 'attributes']/*">
                <saa:attribute name="{saa:getName()}" current="{*[@name = 'current']/@value + *[@name = 'current-mod']/@value}" maximum="{*[@name = 'maximum']/@value}" />
            </xsl:for-each>

            <xsl:variable name="age" select=".//*[@name = 'age']/*" />
            <saa:age current="{$age[@name='current']/@value}" maximum="{$age[@name='maximum']/@value}" />
        </saa:race>
    </xsl:template>

    <xsl:template name="extract-item">
        <xsl:param name="root" select="." />
        <xsl:param name="texts" select="/.." />
        <xsl:param name="id" />

        <saa:item id="{$id}">
            <xsl:variable name="type" select=".//*[@name = 'type']/@value" />
            <xsl:variable name="subtype" select=".//*[@name = 'subtype']/@value" />
            <xsl:variable name="subsubtype" select=".//*[@name = 'subsubtype']/@value" />
            <xsl:variable name="spell-id" select=".//*[@name = 'spell-id']/@value" />
            <xsl:variable name="spell-type" select=".//*[@name = 'spell-type']/@value" />
            <xsl:variable name="spell-dictionary">
                <xsl:choose>
                    <xsl:when test="$spell-type = 0">
                        <xsl:value-of select="'spells-white'" />
                    </xsl:when>
                    <xsl:when test="$spell-type = 1">
                        <xsl:value-of select="'spells-blue'" />
                    </xsl:when>
                    <xsl:when test="$spell-type = 2">
                        <xsl:value-of select="'spells-green'" />
                    </xsl:when>
                    <xsl:when test="$spell-type = 3">
                        <xsl:value-of select="'spells-black'" />
                    </xsl:when>
                    <xsl:when test="$spell-type = 6">
                        <xsl:value-of select="'spells-misc'" />
                    </xsl:when>
                </xsl:choose>
            </xsl:variable>

            <xsl:apply-templates select=".//sse:integer[@name != ''] | .//sse:string" mode="attr" />

            <xsl:apply-templates select=".//*[@name = 'type']" mode="attr" />
            <xsl:apply-templates select=".//*[@name = 'hands']" mode="attr" />
            <xsl:apply-templates select=".//*[@name = 'fingers']" mode="attr" />
            <xsl:apply-templates select=".//*[@name = 'slot']" mode="attr" />
            <xsl:apply-templates select=".//*[@name = 'ammunition-type']" mode="attr" />
            <xsl:apply-templates select=".//*[@name = 'ranged-type']" mode="attr" />

            <xsl:if test=".//*[@name = 'attribute-value']/@value &gt; 0">
                <xsl:apply-templates select=".//*[@name = 'attribute-type']" mode="attr" />
            </xsl:if>
            <xsl:if test=".//*[@name = 'skill-value']/@value &gt; 0">
                <xsl:apply-templates select=".//*[@name = 'skill-type']" mode="attr" />
            </xsl:if>

            <xsl:if test="$spell-id &gt; 0">
                <xsl:attribute name="spell-type">
					<xsl:value-of select="key('string-dictionary', 'spell-types')[position() = $spell-type + 1]/@value" />
				</xsl:attribute>
                <xsl:attribute name="spell-name">
					<xsl:value-of select="key('string-dictionary', $spell-dictionary)[position() = $spell-id]/@value" />
				</xsl:attribute>
            </xsl:if>
            <xsl:attribute name="gender">
				<xsl:choose>
					<xsl:when test=".//*[@name = 'male']/@value &gt; .//*[@name = 'female']/@value">männlich</xsl:when>
					<xsl:when test=".//*[@name = 'male']/@value &lt; .//*[@name = 'female']/@value">weiblich</xsl:when>
					<xsl:otherwise>beide</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
            <xsl:for-each select=".//*[@name = 'properties']/*[@value != '']">
                <xsl:attribute name="is-{saa:getName()}" />
            </xsl:for-each>
            <xsl:for-each select=".//*[@name = 'classes']/*[@value != '']">
                <saa:class-reference name="{saa:getName()}" />
            </xsl:for-each>
            <xsl:if test="$type = 8">
                <xsl:call-template name="extract-text">
                    <xsl:with-param name="root" select="$texts/*[position() = $subtype]/*/*[position() = ($subsubtype + 1)]//@value" />
                    <xsl:with-param name="id" select="0" />
                </xsl:call-template>
            </xsl:if>
        </saa:item>
    </xsl:template>

    <xsl:template name="extract-text">
        <xsl:param name="root" select="." />
        <xsl:param name="id" />
        <saa:text id="{$id}">
            <xsl:for-each select="str:split($root, '^')">
                <xsl:value-of select="translate(., '$×', '&#160;ß')" />
                <br xmlns="http://www.w3.org/1999/xhtml" />
            </xsl:for-each>
        </saa:text>
    </xsl:template>




    <xsl:template match="sse:integer | sse:signed-integer | sse:string" mode="attr">
        <xsl:param name="name" select="@name" />
        <xsl:param name="value" select="@value" />
        <xsl:attribute name="{$name}"><xsl:value-of select="normalize-space($value)" /></xsl:attribute>
    </xsl:template>

    <xsl:template match="sse:select" mode="attr">
        <xsl:param name="name" select="@name" />
        <xsl:variable name="option" select="saa:getDictionaryOption(@dictionary-ref, @value)" />
        <xsl:attribute name="{$name}">
			<xsl:value-of select="$option/@title | $option/@val[not($option/@title)]" />
		</xsl:attribute>
    </xsl:template>

    <xsl:template match="sse:group" mode="unknown">
        <unknown>
            <xsl:for-each select="*">
                <xsl:if test="position() &gt; 1">
                    <xsl:text> </xsl:text>
                </xsl:if>
                <xsl:value-of select="@value" />
                <!-- <xsl:value-of select="str:align(@value, '000', 'right')"/> -->
            </xsl:for-each>
        </unknown>
    </xsl:template>

    <xsl:template match="sse:instruction[@type='bit-field']" mode="reference">
        <xsl:param name="name" select="@name" />
        <xsl:param name="dictionary-ref" select="@dictionary-ref" />

        <xsl:variable name="options" select="saa:getDictionary($dictionary-ref)" />
        <xsl:for-each select="*">
            <xsl:variable name="id" select="position() - 1" />
            <xsl:if test="@value">
                <xsl:element name="saa:{$name}" namespace="http://schema.slothsoft.net/amber/amberdata">
                    <xsl:attribute name="id"><xsl:value-of select="$id" /></xsl:attribute>
                    <xsl:if test="$options">
                        <xsl:attribute name="name"><xsl:value-of select="$options[@key = $id]/@val" /></xsl:attribute>
                    </xsl:if>
                </xsl:element>
            </xsl:if>
        </xsl:for-each>
    </xsl:template>

</xsl:stylesheet>