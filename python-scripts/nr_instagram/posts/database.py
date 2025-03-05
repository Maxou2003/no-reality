import mysql.connector


class Database:
    def __init__(self, database, host="localhost", user="root", password=""):
        self.database = database
        self.host = host
        self.user = user
        self.password = password

    def connect(self):
        return mysql.connector.connect(
            host=self.host,
            user=self.user,
            password=self.password,
            database=self.database
        )

    def fetch_all(self, query, params=None):
        try:
            conn = self.connect()
            cursor = conn.cursor()
            cursor.execute(query, params or [])
            rows = cursor.fetchall()
            cursor.close()
            conn.close()
            return rows
        except mysql.connector.Error as err:
            print(f"Erreur MySQL: {err}")
            return []

    def fetch_one(self, query, params=None):
        try:
            conn = self.connect()
            cursor = conn.cursor()
            cursor.execute(query, params or [])
            row = cursor.fetchone()
            cursor.close()
            conn.close()
            return row
        except mysql.connector.Error as err:
            print(f"Erreur MySQL: {err}")
            return None

    def execute(self, query, params):
        try:
            conn = self.connect()
            cursor = conn.cursor()
            cursor.execute(query, params)
            conn.commit()
            last_id = cursor.lastrowid
            cursor.close()
            conn.close()
            return last_id
        except mysql.connector.Error as err:
            print(f"Erreur MySQL: {err}")
            return None
