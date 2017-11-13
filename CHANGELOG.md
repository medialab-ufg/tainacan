# Changelog
Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
e este projeto adere para [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.7] -
### Adicionado

### Modificado

### Corrigido

### Removido

### Obsoleto

## [0.6] - 2017-11-15
### Adicionado
- Bbotão de pesquisar no fim da interface de busca avançada
- Botão de ocultar os filtros ao lado de botão pesquisar
- Bbotão "Voltar ao topo" na tela de submissão de arquivos
- Facilitar a remoção de muitas categorias de uma vez em "Minhas categorias'
- Habilitação do botão para adicionar novas categorias

### Modificado
- Melhoria na interface "Minhas Categorias"
- Atualização de redirecionamentos e urls
- Melhoria no envio de itens ao sistema, ao enviar seus ids e thumbnails.
- Otimizar o envio de PDFs em grandes quantidades e tamanhos
- Melhoria nas notificações e eventos do sistema
- Alterar a mensagem exibida na ativação/desativação de licenças
- Permitir a submissão de imagens retangulares na marca do repositorio
- Aplicação de tema nas coleções
- Metadados de categorias apresentado nos formulários de submissão de itens com espaçamentos em branc


### Corrigido
- API: erro de retorno de thumbnail
- Metadado de texto longo não permite inserir múltiplos valores
- Definição de licença na submissão do item, mas não sua apresentação na visualização do item
- Correção da seleção de licenças
- Correção do relacionamento de categorias com item
- Não persistencia do tipo do item na submissão de site via URL
- Não relacionamento de categorias ao item na submissão de item
- Navegação e redirecionamento para a pagina de Perfil
- Travamento no envio de itens na submissão por arquivos
- Não carregamento de arquivos .docx na submissão de itens
- Apos ocultar os metadados, se o usuário criar um novo metadado, os metadados que estavam ocultados voltam a ficar ativos
- Não submissão de marca e capa do repositorio
- Submissão de itens por arquivo comprimido
- Validação de data inválida nos metadados do tipo data
- Não ocultação de metadados públicos
- Ao editar um item do tipo 'Texto' e passar o tipo para 'Outro' (URL) não ocorreu a mudança de interface. Não foi possível inserir a url e continuou a aparecer a caixa de texto.
- Busca principal na home inicial
- Não submissão de grandes quantidades de imagens via 'Adição por arquivos'
- Permissão de exclusão de item proprietário em uma coleção não proprietária
- Não relacionamento dos metadados padrões e fixos do repositório a um item
- Não apresentação expandida das categorias na faceta de filtros de uma coleção no MAC-OS

### Removido
- Remoção de opções não utilziadas na tela de registro de usuário

### Obsoleto
- Extração de metadados Exif

## [0.5] - 2017-10-09

## [0.4] - 2017-09-21

## [0.3] - 2017-04-12

[Unreleased]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.2...HEAD
[0.7]: https://github.com/medialab-ufg/tainacan/compare/v0.6...v0.7
[0.6]: https://github.com/medialab-ufg/tainacan/compare/v0.5...v0.6
[0.5]: https://github.com/medialab-ufg/tainacan/compare/v0.4...v0.5
[0.4]: https://github.com/medialab-ufg/tainacan/compare/v0.3...v0.4
[0.3]: https://github.com/medialab-ufg/tainacan/compare/v0.2...v0.3
