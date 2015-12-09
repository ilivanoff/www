/*
 * + FILE [tai_ps_timeline.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_ps_timeline;

CREATE TRIGGER tai_ps_timeline AFTER INSERT
  ON ps_timeline FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline', 'T');
END
|

delimiter ;

/*
 * + FILE [tau_ps_timeline.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_ps_timeline;

CREATE TRIGGER tau_ps_timeline AFTER UPDATE
  ON ps_timeline FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline', 'T');
END
|

delimiter ;

/*
 * + FILE [tad_ps_timeline.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_ps_timeline;

CREATE TRIGGER tad_ps_timeline AFTER DELETE
  ON ps_timeline FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_ps_timeline_item.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_ps_timeline_item;

CREATE TRIGGER tai_ps_timeline_item AFTER INSERT
  ON ps_timeline_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline_item', 'T');
END
|

delimiter ;

/*
 * + FILE [tau_ps_timeline_item.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_ps_timeline_item;

CREATE TRIGGER tau_ps_timeline_item AFTER UPDATE
  ON ps_timeline_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline_item', 'T');
END
|

delimiter ;

/*
 * + FILE [tad_ps_timeline_item.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_ps_timeline_item;

CREATE TRIGGER tad_ps_timeline_item AFTER DELETE
  ON ps_timeline_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline_item', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_ps_gallery.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_ps_gallery;

CREATE TRIGGER tai_ps_gallery AFTER INSERT
  ON ps_gallery FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery', 'T');
END
|

delimiter ;

/*
 * + FILE [tau_ps_gallery.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_ps_gallery;

CREATE TRIGGER tau_ps_gallery AFTER UPDATE
  ON ps_gallery FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery', 'T');
END
|

delimiter ;

/*
 * + FILE [tad_ps_gallery.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_ps_gallery;

CREATE TRIGGER tad_ps_gallery AFTER DELETE
  ON ps_gallery FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_ps_gallery_images.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_ps_gallery_images;

CREATE TRIGGER tai_ps_gallery_images AFTER INSERT
  ON ps_gallery_images FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery_images', 'T');
END
|

delimiter ;

/*
 * + FILE [tau_ps_gallery_images.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_ps_gallery_images;

CREATE TRIGGER tau_ps_gallery_images AFTER UPDATE
  ON ps_gallery_images FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery_images', 'T');
END
|

delimiter ;

/*
 * + FILE [tad_ps_gallery_images.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_ps_gallery_images;

CREATE TRIGGER tad_ps_gallery_images AFTER DELETE
  ON ps_gallery_images FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery_images', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_ps_lib_item.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_ps_lib_item;

CREATE TRIGGER tai_ps_lib_item AFTER INSERT
  ON ps_lib_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_lib_item', 'T');
END
|

delimiter ;

/*
 * + FILE [tau_ps_lib_item.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_ps_lib_item;

CREATE TRIGGER tau_ps_lib_item AFTER UPDATE
  ON ps_lib_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_lib_item', 'T');
END
|

delimiter ;

/*
 * + FILE [tad_ps_lib_item.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_ps_lib_item;

CREATE TRIGGER tad_ps_lib_item AFTER DELETE
  ON ps_lib_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_lib_item', 'T');
END
|

delimiter ;