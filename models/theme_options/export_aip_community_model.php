<?php
/**
 * Model que realiza a exportacao do zip AIP do tainacan
 */
class ExportAIPRepositoryModel extends ExportAIPModel {
    
    public $XML;
    public $name_folder_community;
    
    /**
     * metodo que executa os demais para criacao do mets e do zip do repositorio
     */
    public function create_communities() {
        $this->name_folder_community = 'COMMUNITY@'.$this->prefix.'-'. get_option('collection_root_id');
        $dir_community = $this->dir.'/'.$this->name_folder.'/'.$this->name_folder_repository;
        $this->recursiveRemoveDirectory($dir_community);
        if(!is_dir($dir_community.'/')){
             mkdir($dir_community);
        }
        $this->generate_xml();
        $this->create_xml_file($dir_community.'/mets.xml', $this->XML);
        $this->create_zip_by_folder($this->dir.'/'.$this->name_folder.'/', $this->name_folder_community.'/', $this->name_folder_community,true);
        $this->recursiveRemoveDirectory($dir_community);
    }
    
    
    public function generate_xml(WP_Post $commnutiy){
        $this->XML = '<?xml version="1.0" encoding="utf-8" standalone="no"?>';
        $this->XML .= '<mets ID="DSpace_COMMUNITY_'.$this->prefix.'-'.$commnutiy->ID.'" OBJID="hdl:'.$this->prefix.'/'.$commnutiy->ID.'" TYPE="DSpace SITE" '
                . 'PROFILE="http://www.dspace.org/schema/aip/mets_aip_1_0.xsd" '
                . 'xmlns="http://www.loc.gov/METS/" '
                . 'xmlns:xlink="http://www.w3.org/1999/xlink" '
                . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                . 'xsi:schemaLocation="http://www.loc.gov/METS/ http://www.loc.gov/standards/mets/mets.xsd">';
        $this->XML .= trim('<metsHdr>
                            <agent ROLE="CUSTODIAN" TYPE="OTHER" OTHERTYPE="DSpace Archive">
                                <name>ri/0</name>
                            </agent>
                            <agent ROLE="CREATOR" TYPE="OTHER" OTHERTYPE="DSpace Software">
                                <name>DSpace 5.5</name>
                            </agent>
                       </metsHdr>');
        $this->XML .= '<dmdSec ID="dmdSec_1">
                        <mdWrap MDTYPE="MODS">
                         <xmlData xmlns:mods="http://www.loc.gov/mods/v3" 
                         xmlns:xlink="http://www.w3.org/1999/xlink" 
                         xsi:schemaLocation="http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-1.xsd">
                         <mods:mods xmlns:mods="http://www.loc.gov/mods/v3" 
                         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                         xsi:schemaLocation="http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-1.xsd">
                         <mods:note>'.$commnutiy->post_content.'</mods:note>
                        <mods:abstract />
                        <mods:tableOfContents />
                        <mods:identifier type="uri">'. get_the_permalink($commnutiy->ID).'</mods:identifier>
                        <mods:accessCondition type="useAndReproduction" />
                        <mods:titleInfo>
                          <mods:title>'.$commnutiy->post_title.'</mods:title>
                        </mods:titleInfo>
                      </mods:mods></xmlData>
                        </mdWrap>
                       </dmdSec>
                      ';
        $this->XML .= '<dmdSec ID="dmdSec_2">
                            <mdWrap MDTYPE="OTHER" OTHERMDTYPE="DIM">
                             <xmlData xmlns:dim="http://www.dspace.org/xmlns/dspace/dim"><dim:dim xmlns:dim="http://www.dspace.org/xmlns/dspace/dim" dspaceType="Community">
                                <dim:field mdschema="dc" element="description" >'.$commnutiy->post_content.'</dim:field>
                                <dim:field mdschema="dc" element="description" qualifier="abstract" />
                                <dim:field mdschema="dc" element="description" qualifier="tableofcontents" />
                                <dim:field mdschema="dc" element="identifier" qualifier="uri">hdl:'.$this->prefix.'/'.$commnutiy->ID.'</dim:field>
                                <dim:field mdschema="dc" element="rights" />
                                <dim:field mdschema="dc" element="title">'.$commnutiy->post_title.'</dim:field>
                                </dim:dim>
                              </xmlData>
                            </mdWrap>
                        </dmdSec>
                      ';
        $this->generate_xml_groups_and_users();
        $this->generate_community_xml();
        $this->XML .= '</mets>';
    }
    
    /**
     *  gera o xml dos grupos do tainacan
     */
    public function generate_xml_groups(WP_Post $commnutiy){
        $this->XML .= '
            <amdSec ID="amd_3">
                <techMD ID="techMD_5">
                 <mdWrap MDTYPE="OTHER" OTHERMDTYPE="DSPACE-ROLES">
                  <xmlData xmlns:dsroles="http://www.dspace.org/xmlns/dspace/dspace-roles">
                    <DSpaceRoles>
                         <Groups>
                             <Group ID="'.$this->get_moderators_collection_id($commnutiy->ID).'" Name="administrator_'.$commnutiy->ID.'">'.
                                        $this->get_users_moderators('admin')
                            .'</Group>
                         </Group>'; 
        $this->XML .= '</DSpaceRoles>
                   </xmlData>
                </mdWrap>
              </techMD>
              <rightsMD ID="rightsMD_9">
                    <mdWrap MDTYPE="OTHER" OTHERMDTYPE="METSRIGHTS">
                        <xmlData xmlns:rights="http://cosimo.stanford.edu/sdr/metsrights/" xsi:schemaLocation="http://cosimo.stanford.edu/sdr/metsrights/ http://cosimo.stanford.edu/sdr/metsrights.xsd"><rights:RightsDeclarationMD xmlns:rights="http://cosimo.stanford.edu/sdr/metsrights/" RIGHTSCATEGORY="LICENSED">
                            <rights:Context in-effect="true" CONTEXTCLASS="GENERAL PUBLIC">
                              <rights:Permissions DISCOVER="true" DISPLAY="true" MODIFY="false" DELETE="false" />
                            </rights:Context>
                            <rights:Context in-effect="true" CONTEXTCLASS="MANAGED GRP">
                              <rights:UserName USERTYPE="GROUP">administrator_'.$commnutiy->ID.'</rights:UserName>
                              <rights:Permissions DISCOVER="true" DISPLAY="true" COPY="true" DUPLICATE="true" MODIFY="true" DELETE="true" PRINT="true" OTHER="true" OTHERPERMITTYPE="ADMIN" />
                            </rights:Context>
                          </rights:RightsDeclarationMD>
                    </xmlData>
                </mdWrap>
               </rightsMD>
                <sourceMD ID="sourceMD_10">
                 <mdWrap MDTYPE="OTHER" OTHERMDTYPE="AIP-TECHMD">
                    <xmlData xmlns:dim="http://www.dspace.org/xmlns/dspace/dim"><dim:dim xmlns:dim="http://www.dspace.org/xmlns/dspace/dim" dspaceType="COMMUNITY">
                        <dim:field mdschema="dc" element="identifier" qualifier="uri">hdl:'.$this->prefix.'/'.$commnutiy->ID.'</dim:field>
                        <dim:field mdschema="dc" element="relation" qualifier="isPartOf">hdl:'.$this->prefix.'/0</dim:field>
                      </dim:dim>
                    </xmlData>
                 </mdWrap>
                </sourceMD>
            </amdSec>'; 
    }
    
    /**
     * busca no banco os usuario para cada role
     */
    public function get_users_moderators(WP_Post $community) {
        $blogusers = $this->get_moderators($community->ID);
        if($blogusers){
            $valor .= '<Members>';
            foreach ( $blogusers as $user ) {
                    $user = get_user_by('id', $user);
                    $this->XML .= '<Member ID="'.$user->ID.'" Name="' . esc_html( $user->user_email ) . '" />';
            }
            $valor .= '</Members>';
        }
        return $valor;
    }
    
    /**
     * 
     * @param type $param
     */
    public function getFileThumbnail($param) {
        
    }
    /**
     * metodo que cria a estrutura das comunidades
     */
    public function generate_community_xml() {
        $this->XML .= '<structMap ID="struct_11" LABEL="DSpace Object" TYPE="LOGICAL">';
        $this->XML .= '<div ID="div_12" DMDID="dmdSec_2 dmdSec_1" ADMID="amd_3" TYPE="DSpace Object Contents">';
        $this->XML .= '<div ID="div_13" TYPE="DSpace COMMUNITY">';
        $this->XML .= '<mptr ID="mptr_14" LOCTYPE="HANDLE" xlink:type="simple" xlink:href="'.$this->prefix.'/'. get_option('collection_root_id').'"/>';
        $this->XML .= '<mptr ID="mptr_15" LOCTYPE="URL" xlink:type="simple" xlink:href="COMMUNITY@'.$this->prefix.'-'. get_option('collection_root_id').'.zip"/>';
        $this->XML .= '</div>';
        $this->XML .= '</div>';
        $this->XML .= '</structMap>';
    }
    
    /**
     * metodo que busca os meradores de uma colecao
     * @param int $id
     */
    public function get_moderators($id) {
        $moderators_array = [];
        $owner = get_post($id)->post_author;
        $moderators = get_post_meta($id, 'socialdb_collection_moderator');
        if(is_array($moderators)){
            $moderators_array = array_unique(array_filter($moderators));
        }
        $moderators_array[] = $owner;
        return $moderators_array;
    }
    
}
