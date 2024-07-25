function gritter(growlPesan,growlType='info'){
  $.bootstrapGrowl(growlPesan, {
    type: growlType,
    delay: 2500,
    allow_dismiss: true
  });
}
