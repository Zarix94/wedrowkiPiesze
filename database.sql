CREATE TABLE IF NOT EXISTS users(
  id                      BIGSERIAL PRIMARY KEY,
  login                   VARCHAR(50),
  password                VARCHAR(255),
  email                   VARCHAR(255),
  deleted                 TIMESTAMP,
  created                 TIMESTAMP default now()
);

CREATE TABLE IF NOT EXISTS walking_tour(
  id                      BIGSERIAL PRIMARY KEY,
  user_id                 BIGINT REFERENCES users(id),
  name                    VARCHAR(80),
  description             TEXT,
  date_start              DATE,
  date_stop               DATE,
  km                      SMALLINT,
  deleted                 TIMESTAMP,
  created                 TIMESTAMP default now()
);

CREATE TABLE IF NOT EXISTS place(
  id                      BIGSERIAL PRIMARY KEY,
  name                    VARCHAR(80),
  visit_date              DATE,
  tour_id                 BIGINT REFERENCES walking_tour(id),
  deleted                 TIMESTAMP,
  created                 TIMESTAMP default now()
);

CREATE TABLE IF NOT EXISTS file(
  id                      BIGSERIAL PRIMARY KEY,
  src                     VARCHAR(255),
  type                    SMALLINT,
  tour_id                 BIGINT REFERENCES walking_tour(id),
  created                 TIMESTAMP default now()
);
