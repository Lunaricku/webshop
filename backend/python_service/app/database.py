from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker, declarative_base
import time
from sqlalchemy.exc import OperationalError

DATABASE_URL = "mysql+pymysql://user:password@mysql:3306/userdb"

engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

for i in range(10):
    try:
        engine = create_engine(DATABASE_URL)
        connection = engine.connect()
        print("✅ Connected to database")
        break
    except OperationalError as e:
        print(f"⏳ DB not ready yet, retrying ({i+1}/10)...")
        time.sleep(3)
else:
    raise Exception("❌ Failed to connect to DB after 10 retries.")