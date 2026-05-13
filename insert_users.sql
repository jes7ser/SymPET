INSERT IGNORE INTO user (id, email, roles, password, is_enabled) VALUES
(1, 'admin@sympet.com', '["ROLE_ADMIN"]', '$2y$13$C7OsI0MvFbaRKSK7YDN6A.LP1M0Ld.Dp3BUZ908T0.dCKW2AYJvi6', 1),
(2, 'marie.lacombe@baudry.fr', '["ROLE_USER"]', '$2y$13$LcPcCc32sUJzfKLGVkfV4.9obPj9SGX0SeuHqHfuRp7FhIQ6aOuQy', 1),
(3, 'begue.benoit@tele2.fr', '["ROLE_USER"]', '$2y$13$pKxj5YStAqvfXiwLC2CK7.zy1xy3aI7OwavnWuXflR1bCjwR4Om7u', 1),
(4, 'dmuller@orange.fr', '["ROLE_USER"]', '$2y$13$4y6tUqO80Rg09ByD24QLFOkvtx3pXhRuCOzpygst4DFLlG7vg2BTW', 1),
(5, 'deschamps.jacques@orange.fr', '["ROLE_USER"]', '$2y$13$wBLD3TSepUTQDBwYZKmbsOEB.41C4584nMU2ukf0mNcSNbQKT/6BW', 1);
