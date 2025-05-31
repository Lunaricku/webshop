from fastapi import APIRouter, Depends, Form, Request
from sqlalchemy.orm import Session
from .database import SessionLocal
from .models import User
from .entity import UserEntity
import logging
from pydantic import BaseModel
from fastapi.responses import JSONResponse

logger = logging.getLogger("uvicorn")
router = APIRouter()
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

@router.post("/register")
def register(request: UserEntity, db: Session = Depends(get_db)):
    # data = request.json()
    logger.info(f"username: {request.username}")
    logger.info(f"password: {request.password}")
    # logger.info(f"Received data: {data}")
    # username = data.get("username")
    # password = data.get("password")
    user = User(username=request.username, password=request.password)
    db.add(user)
    db.commit()
    return JSONResponse(content={"msg": "User created"}, status_code=200)

@router.get("/hello")
def hello():
    return {"msg": "Hello Cun Con 🐶"}
