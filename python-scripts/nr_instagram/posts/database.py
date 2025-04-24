import sys
import os
root_path = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
if root_path not in sys.path:
    sys.path.insert(0, root_path)

from config import HOST, USER, PASSWORD
import mysql.connector

class Database:
    def __init__(self, database, host=HOST, user=USER, password=PASSWORD):
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
