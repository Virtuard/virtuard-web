SELECT id, name, user_name FROM users WHERE user_name = 'katryn_le_noor' OR user_name LIKE '%katryn%';
SELECT * FROM user_game_progress WHERE user_id = (SELECT id FROM users WHERE user_name = 'katryn_le_noor');
