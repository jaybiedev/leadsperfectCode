CREATE OR REPLACE FUNCTION get_menu_id (text)
RETURNS INT AS $$
DECLARE menu_id INT;
BEGIN
    SELECT id INTO menu_id FROM menu WHERE path = $1;
    RETURN menu_id;
END;
$$  LANGUAGE plpgsql VOLATILE;