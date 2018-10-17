<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns="http://schema.slothsoft.net/amber/amberdata"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
	xmlns:sse="http://schema.slothsoft.net/savegame/editor"
	xmlns:sfx="http://schema.slothsoft.net/farah/xslt"
	xmlns:exsl="http://exslt.org/common"
	xmlns:set="http://exslt.org/sets">
	
	<xsl:import href="farah://slothsoft@farah/xsl/xslt" />
	
	<xsl:variable name="AM2" select=".//sse:archive[@name='AM2_BLIT' or @name='AM2_CPU']"/>
	<xsl:variable name="Dict" select=".//sse:archive[@name='Dictionary.german']"/>
	<xsl:variable name="MapTexts" select=".//sse:archive[@name='1Map_texts.amb' or @name='2Map_texts.amb' or @name='3Map_texts.amb']"/>
	<xsl:variable name="PCs" select=".//sse:archive[@name='Party_char.amb']"/>
	<xsl:variable name="NPCs" select=".//sse:archive[@name='NPC_char.amb']"/>
	<xsl:variable name="Monsters" select=".//sse:archive[@name='Monster_char_data.amb']"/>
	<xsl:variable name="MonsterGroups" select=".//sse:archive[@name='Monster_groups.amb']"/>

	<xsl:template match="/*">
		<amberdata version="0.1">
			<xsl:apply-templates select="*/sse:savegame.editor" />
		</amberdata>
	</xsl:template>
	
	<xsl:template match="sse:savegame.editor">
		<dictionary-list>
			<xsl:call-template name="dictionary.characters"/>
			<xsl:call-template name="dictionary.monsters"/>
			
			<xsl:call-template name="dictionary.keywords"/>
			<xsl:call-template name="dictionary.items"/>
			<xsl:call-template name="dictionary.spells"/>
			
			<xsl:call-template name="dictionary.maps"/>
			<xsl:call-template name="dictionary.tilesets"/>
			
			<xsl:call-template name="dictionary.places"/>
			
			<xsl:call-template name="dictionary.events"/>
		</dictionary-list>
	</xsl:template>
	
	<xsl:template name="dictionary.spells">
		<xsl:comment>dictionary.spells</xsl:comment>
		<xsl:apply-templates select="$AM2//sse:instruction[@name = 'spell-types']" mode="dictionary"/>
		<xsl:for-each select="$AM2//sse:group[@name = 'spell-names']/*">
			<xsl:apply-templates select="." mode="dictionary">
				<xsl:with-param name="name" select="concat('spells-', position() - 1)"/>
			</xsl:apply-templates>
		</xsl:for-each>
		<dictionary dictionary-id="spell-targets">
			<option key="0" val="-" />
			<option key="1" val="single friend" />
			<option key="2" val="row of friends" />
			<option key="4" val="all friends" />
			<option key="8" val="single enemy" />
			<option key="16" val="row of enemies" />
			<option key="32" val="all enemies" />
			<option key="64" val="single item" />
			<option key="128" val="friend and spot" />
		</dictionary>
		<dictionary dictionary-id="spell-places">
			<option key="0" val="outside" />
			<option key="1" val="in buildings" />
			<option key="2" val="in dungeons" />
			<option key="3" val="while resting" />
			<option key="4" val="in combat" />
			<option key="5" val="lyramion" />
			<option key="6" val="forest moon" />
			<option key="7" val="desert moon" />
		</dictionary>
		<dictionary dictionary-id="spell-modifiers">
			<option key="0" val="0" />
			<option key="1" val="1" />
			<option key="2" val="2" />
			<option key="3" val="3" />
			<option key="4" val="4" />
			<option key="5" val="5" />
			<option key="6" val="6" />
			<option key="7" val="7" />
		</dictionary>
		<dictionary dictionary-id="spell-effects">
			<option key="0" val="Heile Irritation" />
			<option key="1" val="Heile Verrücktheit" />
			<option key="2" val="Heile Schlaf" />
			<option key="3" val="Heile Panik" />
			<option key="4" val="Heile Erblindet" />
			<option key="5" val="Heile Stoned" />
			<option key="6" val="Heile Erschöpfung" />
			<option key="7" val="-" />
			<option key="8" val="Heile Gelähmt" />
			<option key="9" val="Heile Vergiftet" />
			<option key="10" val="Heile Versteinert" />
			<option key="11" val="Heile Krank" />
			<option key="12" val="Heile Alterung" />
			<option key="13" val="Heile Tot (Leiche)" />
			<option key="14" val="Heile Tot (Asche)" />
			<option key="15" val="Heile Tot (Staub)" />
			<option key="255" val="Spezial" />
		</dictionary>
	</xsl:template>
	
	<xsl:template name="dictionary.items">
		<xsl:comment>dictionary.items</xsl:comment>
		<xsl:apply-templates select="$AM2//sse:instruction[@name = 'items']" mode="dictionary">
			<xsl:with-param name="name" select="'item-ids'"/>
		</xsl:apply-templates>
		
		<dictionary dictionary-id="item-status">
			<option key="0" val="identified" />
			<option key="1" val="broken" />
		</dictionary>

		<dictionary dictionary-id="item-properties">
			<option key="0" val="cursed" />
			<option key="1" val="purchaseable" />
			<option key="2" val="stackable" />
			<option key="3" val="combat-equippable" />
			<option key="4" val="useable" />
			<option key="5" val="readable" />
			<option key="6" val="cloneable" />
			<option key="7" val="?" />
		</dictionary>

		<dictionary dictionary-id="item-types">
			<option key="21" val="Zustandsveränderung" />
			<option key="1" val="Rüstung" />
			<option key="2" val="Helm" />
			<option key="3" val="Schuhe" />
			<option key="4" val="Schild" />
			<option key="5" val="Nahkampfwaffe" />
			<option key="6" val="Fernkampfwaffe" />
			<option key="7" val="Munition" />
			<option key="8" val="Textrolle" />
			<option key="9" val="Spruchrolle" />
			<option key="10" val="Trank" />
			<option key="11" val="Amulett" />
			<option key="12" val="Brosche" />
			<option key="13" val="Ring" />
			<option key="14" val="Gemme" />
			<option key="15" val="Werkzeug" />
			<option key="16" val="Schlüssel" />
			<option key="17" val="Gegenstand" />
			<option key="18" val="Mag. Gegenstand" />
			<option key="19" val="Spezialgegenstand" />
			<option key="20" val="Transportmittel" />
		</dictionary>

		<dictionary dictionary-id="item-slots">
			<option key="0" val="-" />
			<option key="1" val="Amulett" />
			<option key="2" val="Helm" />
			<option key="3" val="Brosche" />
			<option key="4" val="Waffe" />
			<option key="5" val="Rüstung" />
			<option key="6" val="Schild" />
			<option key="7" val="Ring" />
			<option key="8" val="Schuhe" />
		</dictionary>
	</xsl:template>
	
	<xsl:template name="dictionary.maps">
		<xsl:comment>dictionary.maps</xsl:comment>
		<xsl:variable name="files" select="$MapTexts/sse:file"/>
		<xsl:variable name="worlds" select="$AM2//sse:instruction[@name = 'map-worlds']"/>		
		<xsl:variable name="world-names" select="$worlds//sse:string/@value"/>
		
		<xsl:apply-templates select="$worlds" mode="dictionary"/>
		
		<dictionary dictionary-id="map-ids">
			<option key="0" val="-" />
			<xsl:call-template name="dictionary.maps.world">
				<xsl:with-param name="first-id" select="1"/>
				<xsl:with-param name="name" select="$world-names[1]"/>
				<xsl:with-param name="width" select="16"/>
				<xsl:with-param name="height" select="16"/>
			</xsl:call-template>
			<xsl:call-template name="dictionary.maps.local">
				<xsl:with-param name="first-id" select="257"/>
				<xsl:with-param name="last-id" select="299"/>
				<xsl:with-param name="files" select="$files"/>
			</xsl:call-template>
			<xsl:call-template name="dictionary.maps.world">
				<xsl:with-param name="first-id" select="300"/>
				<xsl:with-param name="name" select="$world-names[2]"/>
				<xsl:with-param name="width" select="6"/>
				<xsl:with-param name="height" select="6"/>
			</xsl:call-template>
			<xsl:call-template name="dictionary.maps.local">
				<xsl:with-param name="first-id" select="336"/>
				<xsl:with-param name="last-id" select="512"/>
				<xsl:with-param name="files" select="$files"/>
			</xsl:call-template>
			<xsl:call-template name="dictionary.maps.world">
				<xsl:with-param name="first-id" select="513"/>
				<xsl:with-param name="name" select="$world-names[3]"/>
				<xsl:with-param name="width" select="4"/>
				<xsl:with-param name="height" select="4"/>
			</xsl:call-template>
		</dictionary>
		<dictionary dictionary-id="map-types">
			<option key="1" val="3D" />
			<option key="2" val="2D" />
		</dictionary>
	</xsl:template>
	
	<xsl:template name="dictionary.maps.world">
		<xsl:param name="name"/>
		<xsl:param name="width"/>
		<xsl:param name="height"/>
		<xsl:param name="first-id"/>
		<xsl:param name="size" select="50"/>
	
		<xsl:for-each select="sfx:range(1, $height)">
			<xsl:variable name="y" select=". - 1"/>
			<xsl:for-each select="sfx:range(1, $width)">
				<xsl:variable name="x" select=". - 1"/>
				
				<xsl:variable name="key" select="$first-id + $y * $width + $x"/>
				<xsl:variable name="val" select="concat($name, ' (', format-number($x * $size, '000'), '|', format-number($y * $size, '000'), ')')"/>
				<option key="{$key}" val="{$val}"/>
			</xsl:for-each>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="dictionary.maps.local">
		<xsl:param name="files"/>
		<xsl:param name="first-id" select="0"/>
		<xsl:param name="last-id" select="65535"/>
		
		<xsl:for-each select="$files[@file-name &gt;= $first-id and @file-name &lt;= $last-id]">
			<option key="{number(@file-name)}" val="{normalize-space(.//sse:string[1]/@value)}" />
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="dictionary.tilesets">
		<xsl:comment>dictionary.tilesets</xsl:comment>
		<dictionary dictionary-id="colors">
			<option key="0" val="00 transparent" />
			<option key="1" val="01 white" />
			<option key="2" val="02 light gray" />
			<option key="3" val="03 medium gray" />
			<option key="4" val="04 dark gray" />
			<option key="5" val="05 really dark gray" />
			<option key="6" val="06 dark brown" />
			<option key="7" val="07 dark yellow" />
			<option key="8" val="08 medium brown" />
			<option key="9" val="09 light brown" />
			<option key="10" val="10" />
			<option key="11" val="11" />
			<option key="12" val="12 blue" />
			<option key="13" val="13 light blue" />
			<option key="14" val="14 yellow" />
			<option key="15" val="15 orange" />
		</dictionary>

		<dictionary dictionary-id="tile-icon-data-0">
			<option key="0" />
			<option key="1" />
			<option key="2" />
			<option key="3" />
			<option key="4" />
			<option key="5" />
			<option key="6" />
			<option key="7" />
		</dictionary>

		<dictionary dictionary-id="tile-icon-data-1">
			<option key="0" />
			<option key="1" />
			<option key="2" />
			<option key="3" />
			<option key="4" />
			<option key="5" />
			<option key="6" />
			<option key="7" />
		</dictionary>

		<dictionary dictionary-id="tile-icon-mobility">
			<option key="0" val="foot" />
			<option key="1" val="horse" />
			<option key="2" val="raft" />
			<option key="3" val="ship" />
			<option key="4" val="broom" />
			<option key="5" val="eagle" />
			<option key="6" val="cape" />
			<option key="7" val="is-water" />
		</dictionary>

		<dictionary dictionary-id="tile-icon-data-3">
			<option key="0" />
			<option key="1" />
			<option key="2" />
			<option key="3" />
			<option key="4" />
			<option key="5" />
			<option key="6" />
			<option key="7" />
		</dictionary>
	</xsl:template>
	
	
	<xsl:template name="dictionary.keywords">
		<xsl:comment>dictionary.keywords</xsl:comment>
		<xsl:apply-templates select="$Dict//sse:instruction[@name = 'keywords']" mode="dictionary"/>
	</xsl:template>
	
	<xsl:template name="dictionary.characters">
		<xsl:comment>dictionary.characters</xsl:comment>
		<xsl:apply-templates select="$PCs" mode="dictionary">
			<xsl:with-param name="name" select="'pc-ids'"/>
		</xsl:apply-templates>
		<xsl:apply-templates select="$NPCs" mode="dictionary">
			<xsl:with-param name="name" select="'npc-ids'"/>
		</xsl:apply-templates>
		
		<dictionary dictionary-id="character-types">
			<option key="0" val="Partymitglied" />
			<option key="1" val="NPC" />
			<option key="2" val="Monster" />
		</dictionary>

		

		<dictionary dictionary-id="ailments">
			<option key="0" val="Irritation" />
			<option key="1" val="Verrücktheit" />
			<option key="2" val="Schlaf" />
			<option key="3" val="Panik" />
			<option key="4" val="Erblindet" />
			<option key="5" val="Stoned" />
			<option key="6" val="Erschöpfung" />
			<option key="7" val="-" />
			<option key="8" val="Gelähmt" />
			<option key="9" val="Vergiftet" />
			<option key="10" val="Versteinert" />
			<option key="11" val="Krank" />
			<option key="12" val="Alterung" />
			<option key="13" val="Tot (Leiche)" />
			<option key="14" val="Tot (Asche)" />
			<option key="15" val="Tot (Staub)" />
		</dictionary>
		
		<dictionary dictionary-id="languages">
			<option key="0" val="Mensch" />
			<option key="1" val="Elfisch" />
			<option key="2" val="Zwergisch" />
			<option key="3" val="Gnomisch" />
			<option key="4" val="Sylphisch" />
			<option key="5" val="Felinisch" />
			<option key="6" val="Morag" />
			<option key="7" val="Tiersprache" />
		</dictionary>

		<dictionary dictionary-id="attributes">
			<option key="0" val="STÄ" description="Stärke" />
			<option key="1" val="INT" description="Intelligenz" />
			<option key="2" val="GES" description="Geschicklichkeit" />
			<option key="3" val="SCH" description="Schnelligkeit" />
			<option key="4" val="KON" description="Konstitution" />
			<option key="5" val="KAR" description="Karisma" />
			<option key="6" val="GLÜ" description="Glück" />
			<option key="7" val="A-M" description="Anti-Magie" />
		</dictionary>

		<dictionary dictionary-id="skills">
			<option key="0" val="ATT" description="Attacke" />
			<option key="1" val="PAR" description="Parade" />
			<option key="2" val="SCH" description="Schwimmen" />
			<option key="3" val="KRI" description="Kritische Treffer" />
			<option key="4" val="F-F" description="Fallen Finden" />
			<option key="5" val="F-E" description="Fallen Entschärfen" />
			<option key="6" val="S-Ö" description="Schlösser Knacken" />
			<option key="7" val="SUC" description="Suchen" />
			<option key="8" val="SRL" description="Spruchrollen Lesen" />
			<option key="9" val="M-B" description="Magie Benutzen" />
		</dictionary>

		<dictionary dictionary-id="equipment-slots">
			<option key="0" val="Amulett" />
			<option key="1" val="Helm" />
			<option key="2" val="Brosche" />
			<option key="3" val="Waffe" />
			<option key="4" val="Rüstung" />
			<option key="5" val="Schild" />
			<option key="6" val="Ring" />
			<option key="7" val="Schuhe" />
			<option key="8" val="Ring" />
		</dictionary>

		<dictionary dictionary-id="genders">
			<option key="0" val="männlich" />
			<option key="1" val="weiblich" />
		</dictionary>

		<dictionary dictionary-id="races">
			<option key="0" val="Mensch" />
			<option key="1" val="Elf" />
			<option key="2" val="Zwerg" />
			<option key="3" val="Gnom" />
			<option key="4" val="Halb-Elf" />
			<option key="5" val="Sylphe" />
			<option key="6" val="Feline" />
			<option key="7" val="Moraner" />
			<option key="8" val="Thalioner" />
			<option key="13" val="(leer)" />
			<option key="14" val="Tier" />
		</dictionary>

		<dictionary dictionary-id="classes">
			<option key="0" val="Abenteurer" />
			<option key="1" val="Krieger" />
			<option key="2" val="Paladin" />
			<option key="3" val="Dieb" />
			<option key="4" val="Ranger" />
			<option key="5" val="Heiler" />
			<option key="6" val="Alchemist" />
			<option key="7" val="Mystiker" />
			<option key="8" val="Magier" />
			<option key="9" val="Katze" />
			<option key="10" val="(leer)" />
		</dictionary>

		<dictionary dictionary-id="schools">
			<option key="0" val="Keine" />
			<option key="1" val="Weiß (Paladin)" />
			<option key="129" val="Weiß (Heiler)" />
			<option key="2" val="Blau (Abenteurer)" />
			<option key="130" val="Blau (Alchemist)" />
			<option key="4" val="Grün (Ranger)" />
			<option key="132" val="Grün (Mystiker)" />
			<option key="8" val="Schwarz (Magier)" />
		</dictionary>

		<dictionary dictionary-id="hands">
			<option key="0" val="Beide Hände frei" />
			<option key="1" val="Eine Hand frei" />
			<option key="2" val="Keine Hand frei" />
		</dictionary>

		<dictionary dictionary-id="fingers">
			<option key="0" val="Beide Finger frei" />
			<option key="1" val="Ein Finger frei" />
			<option key="2" val="Kein Finger frei" />
		</dictionary>

		<dictionary dictionary-id="ammunitions">
			<option key="0" val="-" />
			<option key="1" val="Schleuderstein" />
			<option key="2" val="Pfeil" />
			<option key="3" val="Bolzen" />
			<option key="4" val="Schleuderdolch" />
		</dictionary>
	</xsl:template>
	
	<xsl:template name="dictionary.monsters">
		<xsl:comment>dictionary.monsters</xsl:comment>
		<xsl:variable name="monster-ids">
			<xsl:apply-templates select="$Monsters" mode="dictionary">
				<xsl:with-param name="name" select="'monster-ids'"/>
			</xsl:apply-templates>
		</xsl:variable>
		
		<xsl:copy-of select="$monster-ids"/>
		
		<xsl:variable name="monster-names" select="exsl:node-set($monster-ids)/*/*"/>
		
		<xsl:for-each select="$MonsterGroups">
			<dictionary dictionary-id="monstergroup-ids">
				<option key="0" val="-" />
				<xsl:for-each select="sse:file">
					<xsl:variable name="file" select="."/>
					<xsl:variable name="name">
						<xsl:for-each select="set:distinct(.//sse:select[@value != 0]/@value)">
							<xsl:sort select="." data-type="number"/>
							<xsl:variable name="id" select="."/>
							<xsl:variable name="count" select="count($file//sse:select[@value = $id])"/>
							<xsl:if test="position() != 1">
								<xsl:text>, </xsl:text>	
							</xsl:if>
							<xsl:value-of select="$count"/>
							<xsl:text>×</xsl:text>
							<xsl:value-of select="$monster-names[@key = $id]/@val"/>
						</xsl:for-each>
					</xsl:variable>
					<option key="{number(@file-name)}" val="{$name}" />
				</xsl:for-each>
			</dictionary>
		</xsl:for-each>

		<dictionary dictionary-id="monster-types">
			<option key="0" val="undead" />
			<option key="1" val="demon" />
			<option key="2" val="boss" />
			<option key="3" val="animal" />
		</dictionary>

		<dictionary dictionary-id="monster-images">
			<option key="1" val="Gargoyle" />
			<option key="2" val="Untoter" />
			<option key="3" val="Dämon" />
			<option key="4" val="Ork" />
			<option key="5" val="Echse" />
			<option key="6" val="Riese" />
			<option key="7" val="Krieger" />
			<option key="8" val="Morag Drache" />
			<option key="9" val="Golem" />
			<option key="10" val="Sansrie" />
			<option key="11" val="Schwarzmagier" />
			<option key="12" val="Minotaur" />
			<option key="13" val="Nera" />
			<option key="14" val="Magische Wache" />
			<option key="15" val="Feuerdrache" />
			<option key="16" val="Spinne" />
			<option key="17" val="Dieb" />
			<option key="18" val="Biest" />
			<option key="19" val="Energiekugel" />
			<option key="20" val="Morag Magier" />
			<option key="21" val="Antiker Wächter" />
			<option key="22" val="Fluchwespe" />
			<option key="23" val="Tornak" />
			<option key="24" val="Gizzek" />
			<option key="25" val="Morag Maschine" />
		</dictionary>

		<dictionary dictionary-id="sprite-cycles">
			<option key="0" val="idling" />
			<option key="1" val="melee attack" />
			<option key="2" val="ranged attack" />
			<option key="3" val="casting spell" />
			<option key="4" val="taking damage" />
			<option key="5" val="dying" />
			<option key="6" val="battle start" />
			<option key="7" val="?" />
		</dictionary>
	</xsl:template>
	
	<xsl:template name="dictionary.places">
		<xsl:comment>dictionary.places</xsl:comment>
		<dictionary dictionary-id="ailments-healer">
			<option key="0" val="Gelähmt" />
			<option key="1" val="Vergiftet" />
			<option key="2" val="Versteinert" />
			<option key="3" val="Krank" />
			<option key="4" val="Alterung" />
			<option key="5" val="Tot (Leiche)" />
			<option key="6" val="Tot (Asche)" />
			<option key="7" val="Tot (Staub)" />
			<option key="8" val="Verrücktheit" />
			<option key="9" val="Erblindet" />
			<option key="10" val="Stoned" />
			<option key="11" val="HP" />
			<option key="12" val="Fluch" />
		</dictionary>
		
		<dictionary dictionary-id="shops">
			<option key="3" val="Spannenberg, Warenhändler von Spannenberg" />
			<option key="2" val="Spannenberg, Bibliothek der Heiler" />
			<option key="4" val="Spannenberg, Bibliothek der Mystik" />
			<option key="1" val="Spannenberg, Laden der Diebesgilde" />
			<option key="5" val="Alchemistenturm, Bibliothek der Alchemie" />
			<option key="6" val="Alchemistenturm, Laden der Alchemie" />
			<option key="10" val="Burnville, Warenhändler von Burnville" />
			<option key="11" val="Burnville, Nalven's Bibliothek" />
			<option key="7" val="Burnville, Bibliothek der Heiler" />
			<option key="8" val="Burnville, Bibliothek des Milzor" />
			<option key="9" val="Burnville, Theresa's Schmuckladen" />
			<option key="12" val="Newlake, Warenhandel von Newlake" />
			<option key="13" val="Illien, Lendrai's Magische Waffen" />
			<option key="15" val="Illien, Aiolu's Tränke Aller Art" />
			<option key="14" val="Illien, Spruchrollen Bibliothek" />
			<option key="17" val="Gnommine, Brom's Warenhaus" />
			<option key="16" val="Snakesign, Warenhändler von Snakesign" />
			<option key="31" val="Dor Kiredon, Ferrin's Schmiede" />
			<option key="32" val="Dor Grestin, Randor's Lädchen" />
			<option key="18" val="S'Angrila, Warenhändler von S'Angrila" />
			<option key="33" val="-" />
		</dictionary>
		
		<dictionary dictionary-id="places">
			<option key="0" val="-" />
			<option key="1" val="GÄSTEZIMMER VON SPANNENBERG" />
			<option key="2" val="SPANNENBERG DIEBES GILDE" />
			<option key="3" val="SPANNENBERG DIEBES GILDE" />
			<option key="4" val="SPANNENBERG DIEBES GILDE" />
			<option key="5" val="HEILER VON SPANNENBERG" />
			<option key="6" val="HEILER VON SPANNENBERG" />
			<option key="7" val="BIBLIOTHEK DER HEILER" />
			<option key="8" val="SCHLAFSAAL DER HEILER" />
			<option key="9" val="MEISTER KARL'S ATTACKE TRAIN." />
			<option key="10" val="MEISTER ROLF'S PARADE TRAIN." />
			<option key="11" val="TOLIMARS PFERDE" />
			<option key="12" val="WARENHÄNDLER VON SPANNENBERG" />
			<option key="13" val="YNNEP LEBENSMITTEL" />
			<option key="14" val="SAGE VON SPANNENBERG" />
			<option key="15" val="TORLES WERFT" />
			<option key="16" val="LADEN DER DIEBESGILDE" />
			<option key="17" val="NAGIER'S K-TREFFER TRAINING" />
			<option key="18" val="BIBLIOTHEK DER MYSTIK" />
			<option key="19" val="BIBLIOTHEK DER ALCHEMIE" />
			<option key="20" val="LADEN DER ALCHEMIE" />
			<option key="21" val="BIBLIOTHEK DER HEILER" />
			<option key="22" val="HEILER VON BURNVILLE" />
			<option key="23" val="BIBLIOTHEK DES MILZOR" />
			<option key="24" val="THERESA'S SCHMUCKLADEN" />
			<option key="25" val="REZEPTION DES KAKTUS" />
			<option key="26" val="SULP LEBENSMITTEL" />
			<option key="27" val="WARENHÄNDLER VON BURNVILLE" />
			<option key="28" val="NALVEN'S SPRUCH TRAINING" />
			<option key="29" val="NALVEN'S LESE TRAINING" />
			<option key="30" val="NALVEN'S BIBLIOTHEK" />
			<option key="31" val="SCHWIMMSCHULE VON BURNVILLE" />
			<option key="32" val="SCHMIEDE VON BURNVILLE" />
			<option key="33" val="LEBAB'S VERZAUBERUNGEN" />
			<option key="34" val="GASTHAUS 'GEMSTONE'" />
			<option key="35" val="FERRIN'S SCHMIEDE" />
			<option key="36" val="DER HEILER ASRUB" />
			<option key="37" val="DIE SCHÄTZE DES WALDES" />
			<option key="38" val="DAS NEST" />
			<option key="39" val="DRELB'S SUCH TRAINING" />
			<option key="40" val="BROM'S WARENHAUS" />
			<option key="41" val="REZEPTION - ZUM SCHACHT" />
			<option key="42" val="LENDRAIS MAGISCHE WAFFEN" />
			<option key="43" val="MANA - LEBENSMITTEL" />
			<option key="44" val="SPRUCHROLLEN BIBLIOTHEK" />
			<option key="45" val="AIOLU'S TRÄNKE ALLER ART" />
			<option key="46" val="HEILER VON ILLIEN" />
			<option key="47" val="REZEPTION DES ADLER'S" />
			<option key="48" val="SAGE VON SNAKESIGN" />
			<option key="49" val="SCHMIEDE VON SNAKESIGN" />
			<option key="50" val="RATIONEN BEI SCHLANGENFRASS" />
			<option key="51" val="WARENHÄNDLER VON SNAKESIGN" />
			<option key="52" val="HEILER DER SANSRIE" />
			<option key="53" val="REZEPTION DER GÖTTIN" />
			<option key="54" val="SCHMIEDE VON NEWLAKE" />
			<option key="55" val="HAUS DER HEILER" />
			<option key="56" val="SIERPINIM - LEBENSMITTEL" />
			<option key="57" val="WARENHANDEL VON NEWLAKE" />
			<option key="58" val="REZEPTION DES KRATER'S" />
			<option key="59" val="FERRIN'S SCHMIEDE:REPARATUREN" />
			<option key="60" val="FERRIN'S SCHMIEDE:REPARATUREN" />
			<option key="61" val="RANDOR'S LÄDCHEN" />
			<option key="62" val="LEBENSMITTEL IN S'ANGRILA" />
			<option key="63" val="WARENHÄNDLER VON S'ANGRILA" />
			<option key="64" val="SCHMIEDE VON S'ANGRILA" />
		</dictionary>
	</xsl:template>
	
	<xsl:template name="dictionary.events">
		<xsl:comment>dictionary.events</xsl:comment>
		<dictionary dictionary-id="gotos">
			<xsl:for-each select="sfx:range(1, 128)">
				<option key="{.}" val="GOTO {.}"/>
			</xsl:for-each>
			<option key="65535" val="RETURN" />
		</dictionary>
		
		<dictionary dictionary-id="events">
			<option key="0" val="valdyn" />
			<option key="1" val="grandfather" />
			<option key="2" val="flight:blue2green" />
			<option key="3" val="flight:blue2orange" />
			<option key="4" val="flight:green2blue" />
			<option key="5" val="flight:green2orange" />
			<option key="6" val="flight:orange2blue" />
			<option key="7" val="flight:orange2green" />
			<option key="8" val="game over" />
			<option key="255" val="-" />
		</dictionary>
		<dictionary dictionary-id="popup-bitmask">
			<option key="1" val="1" />
			<option key="2" val="2" />
			<option key="3" val="3" />
		</dictionary>
		<dictionary dictionary-id="award-operations">
			<option key="0" val="+=" />
			<option key="6" val="|=" />
		</dictionary>
		
		<dictionary dictionary-id="event-types">
			<option key="0" val="debug" />
			<option key="1" val="teleport" />
			<option key="2" val="door" />
			<option key="3" val="chest" />
			<option key="4" val="popup text" />
			<option key="5" val="spinner" />
			<option key="6" val="take damage" />
			<option key="8" val="riddlemouth" />
			<option key="9" val="award" />
			<option key="10" val="set tile" />
			<option key="11" val="battle" />
			<option key="12" val="place" />
			<option key="13" val="if" />
			<option key="14" val="set" />
			<option key="15" val="if-1d100" />
			<option key="16" val="start" />
			<option key="18" val="create" />
			<option key="17" val="print text" />
			<option key="19" val="prompt" />
			<option key="20" val="play music" />
			<option key="21" val="exit" />
			<option key="22" val="spawn" />
			<option key="23" val="success" />
		</dictionary>

		<dictionary dictionary-id="event-trigger-types">
			<option key="0" val="mention keyword" />
			<option key="1" val="show item" />
			<option key="2" val="give item" />
			<option key="3" val="give gold" />
			<option key="4" val="give food" />
			<option key="5" val="ask to join group" />
			<option key="6" val="ask to leave group" />
			<option key="7" val="start conversation" />
			<option key="8" val="end conversation" />
		</dictionary>

		<dictionary dictionary-id="event-award-types">
			<option key="0" val="attribute" />
			<option key="7" val="language" />
			<option key="8" val="experience" />
			<option key="9" val="alkemstat" />
		</dictionary>

		<dictionary dictionary-id="event-set-types">
			<option key="0" val="global variable" />
			<option key="1" val="map event?" />
			<option key="4" val="map variable" />
			<option key="6" val="take item" />
			<option key="8" val="keyword" />
		</dictionary>

		<dictionary dictionary-id="event-create-types">
			<option key="0" val="item" />
			<option key="1" val="gold" />
			<option key="2" val="food" />
		</dictionary>

		<dictionary dictionary-id="event-if-types">
			<option key="0" val="global variable" />
			<option key="4" val="map variable" />
			<option key="5" val="partymember" />
			<option key="6" val="item owned" />
			<option key="7" val="item used" />
			<option key="9" val="exit" />
			<option key="14" val="touch-action" />
			<option key="20" val="inspect-action" />
		</dictionary>

		<dictionary dictionary-id="event-spawn-types">
			<option key="1" val="horse" />
			<option key="2" val="raft" />
			<option key="3" val="ship" />
			<option key="4" val="hoverdisc" />
			<option key="5" val="giant eagle" />
			<option key="6" val="cape" />
			<option key="7" val="magic broom" />
			<option key="8" val="swimming" />
			<option key="9" val="lizard" />
			<option key="10" val="sandship" />
		</dictionary>

		<dictionary dictionary-id="event-teleport-types">
			<option key="0" val="default" />
			<option key="1" val="teleporter" />
			<option key="2" val="windtor" />
			<option key="3" val="upwards" />
			<option key="5" val="downwards" />
		</dictionary>

		<dictionary dictionary-id="boolean">
			<option key="0" val="false" />
			<option key="1" val="true" />
			<option key="2" val="toggle" />
		</dictionary>

		<dictionary dictionary-id="boolean-reverse">
			<option key="0" val="true" />
			<option key="256" val="false" />
		</dictionary>

		<dictionary dictionary-id="proportionality">
			<option key="0" val="=" />
			<option key="1" val="&lt;" />
		</dictionary>

		<dictionary dictionary-id="target">
			<option key="0" val="leader" />
			<option key="1" val="everyone" />
		</dictionary>

		
		<dictionary dictionary-id="map-directions">
			<option key="0" val="north" />
			<option key="1" val="east" />
			<option key="2" val="south" />
			<option key="3" val="west" />
			<option key="4" val="no change" />
		</dictionary>
		
		<dictionary dictionary-id="songs">
			<option key="0" val="-" />
			<option key="1" val="Who said &quot;Hi Ho&quot; ?" />
			<option key="2" val="Mellow camel funk" />
			<option key="3" val="Close to the hedge" />
			<option key="4" val="Voice of the bagpipe" />
			<option key="5" val="Downtown" />
			<option key="6" val="SHIP" />
			<option key="7" val="Whole lotta dove" />
			<option key="8" val="Horse is no disgrace" />
			<option key="9" val="Don't look Bach" />
			<option key="10" val="Rough waterfront tavern" />
			<option key="11" val="Sapphire fireballs of pure love" />
			<option key="12" val="The Aum remains the same" />
			<option key="13" val="CAPITAL" />
			<option key="14" val="Plodding along" />
			<option key="15" val="Compact disc" />
			<option key="16" val="Riverside travelling blues" />
			<option key="17" val="Nobody's vault but mine" />
			<option key="18" val="La Crypta Strangiato" />
			<option key="19" val="Misty dungeon hop" />
			<option key="20" val="Burn baby burn!" />
			<option key="21" val="Bar brawlin'" />
			<option key="22" val="Psychedelic dune groove" />
			<option key="23" val="Stairway to level 50" />
			<option key="24" val="That hunch is back" />
			<option key="25" val="Chicken soup" />
			<option key="26" val="Dragon chase in creepy dungeon" />
			<option key="27" val="His master's voice" />
			<option key="28" val="To the Unknown" />
			<option key="29" val="Oh no not another magical event" />
			<option key="30" val="The Uh-oh song" />
			<option key="31" val="Owner of a lonely sword" />
			<option key="32" val="GAME OVER" />
			<option key="255" val="[stop]" />
		</dictionary>
	</xsl:template>
	
	
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	<xsl:template match="sse:archive" mode="dictionary">
		<xsl:param name="name" select="@name"/>
		
		<dictionary dictionary-id="{$name}">
			<option key="0" val="-" />
			<xsl:for-each select="sse:file">
				<option key="{number(@file-name)}" val="{normalize-space(.//sse:string[@name = 'name']/@value)}" />
			</xsl:for-each>
		</dictionary>
	</xsl:template>
	
	<xsl:template match="sse:instruction[@type = 'string-dictionary']" mode="dictionary">
		<xsl:param name="name" select="@name"/>
		
		<dictionary dictionary-id="{$name}">
			<xsl:for-each select="*">
				<option key="{position() - 1}" val="{@value}" />
			</xsl:for-each>
		</dictionary>
	</xsl:template>
	
	<xsl:template match="sse:instruction[@type = 'repeat-group']" mode="dictionary">
		<xsl:param name="name" select="@name"/>
		
		<dictionary dictionary-id="{$name}">
			<option key="0" val="-"/>
			<xsl:for-each select="*">
				<option key="{position()}" val="{normalize-space(.//sse:string[@name = 'name']/@value)}" />
			</xsl:for-each>
		</dictionary>
	</xsl:template>
</xsl:stylesheet>