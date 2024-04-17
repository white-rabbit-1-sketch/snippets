extends Node

class_name CollisionService

static func get_directional_collisions(object: Node) -> Array:
	var result = []
	
	for index in range(object.get_slide_count()):
		var h_side
		var v_side
		var collision = object.get_slide_collision(index)
		
		if Vector2.DOWN.dot(collision.normal) > 0.9:
			v_side = Collision.DirectionEnum.UP
		elif Vector2.UP.dot(collision.normal) > 0.1:
			v_side = Collision.DirectionEnum.DOWN
		
		if collision.normal.x > 0:
			h_side = Collision.DirectionEnum.LEFT
		elif collision.normal.x < 0:
			h_side = Collision.DirectionEnum.RIGHT
			
		if h_side or v_side:
			result.append(Collision.new(collision, h_side, v_side))
			
	return result
