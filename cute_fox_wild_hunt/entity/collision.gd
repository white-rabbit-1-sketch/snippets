class_name Collision

enum DirectionEnum {UP, DOWN, LEFT, RIGHT}

var collision
var h_side
var v_side
	
func _init(collision, h_side, v_side):
	self.collision = collision
	self.h_side = h_side
	self.v_side = v_side
