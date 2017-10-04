var el = wp.element.createElement,
registerBlockType = wp.blocks.registerBlockType,
Editable = wp.blocks.Editable,
BlockControls = wp.blocks.BlockControls,
AlignmentToolbar = wp.blocks.AlignmentToolbar,
children = wp.blocks.source.children;

registerBlockType( 'tainacan/collections-list', {
  title: 'Tainacan Coleções',

  icon: 'images-alt',

  category: 'widgets',

  attributes: {
    content: {
      type: 'array',
      source: children( 'p' )
    }
},

edit: function( props ) {
  var content = props.attributes.content,
  alignment = props.attributes.alignment,
  focus = props.focus;

  function onChangeContent( newContent ) {
    props.setAttributes( { content: newContent } );
  }

  function onChangeAlignment( newAlignment ) {
    props.setAttributes( { alignment: newAlignment } );
  }

  return [
    !! focus && el(
      BlockControls,
      { key: 'controls' },
      el(
        AlignmentToolbar,
        {
          value: alignment,
          onChange: onChangeAlignment
        }
      )
    ), 
    el(
      Editable,
      {
        key: 'editable',
        tagName: 'p',
        className: props.className,
        style: { textAlign: alignment },
        onChange: onChangeContent,
        value: content,
        focus: focus,
        onFocus: props.setFocus
      }
  )
];
},

save: function( props ) {
  var content = props.attributes.content;

  return el( 'p', { className: props.className }, content );
},
} );